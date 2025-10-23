<?php

namespace App\Services;

use App\Models\DiscountScheme;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Support\Facades\DB;

class DiscountEngine
{
    /**
     * Waktu penahanan (hold) voucher sebelum di-void otomatis saat checkout tidak jadi (menit).
     * Untuk sederhana, kita tidak membuat job cleaner di tahap 2.
     */
    const HOLD_TTL_MINUTES = 15;

    /**
     * Hitung diskon otomatis terbaik dari semua scheme aktif berdasarkan subtotal.
     * return: [
     *   'amount' => 12000.00,
     *   'scheme' => 'Weekend Boost',
     *   'tier'   => ['type'=>'percent','value'=>12,'min_subtotal'=>250000],
     * ]
     */
    public function computeAutoDiscount(float $subtotal, \DateTimeInterface $now = null): array
    {
        $now = $now ? \Carbon\Carbon::instance($now) : now();
        $best = [
            'amount' => 0.0,
            'scheme' => null,
            'tier' => null,
        ];

        // Semua scheme aktif pada waktu ini
        $schemes = DiscountScheme::query()->activeAt($now)->with('tiers')->get();
        foreach ($schemes as $scheme) {
            // Ambil tier yang memenuhi syarat (min_subtotal) dengan prioritas tertinggi
            $eligible = $scheme->tiers
                ->filter(fn($t) => $subtotal >= (float) $t->min_subtotal)
                ->sortByDesc('priority')
                ->sortByDesc('min_subtotal')
                ->values();

            if ($eligible->isEmpty())
                continue;
            $tier = $eligible->first();

            $amount = 0.0;
            if ($tier->type === 'percent') {
                $amount = round($subtotal * ((float) $tier->value / 100.0));
            } else { // amount (rupiah)
                $amount = (float) $tier->value;
            }

            // Terapkan cap per-scheme jika ada
            if (!is_null($scheme->max_discount_amount)) {
                $amount = min($amount, (float) $scheme->max_discount_amount);
            }

            if ($amount > $best['amount']) {
                $best = [
                    'amount' => $amount,
                    'scheme' => $scheme->name,
                    'tier' => [
                        'type' => $tier->type,
                        'value' => (float) $tier->value,
                        'min_subtotal' => (float) $tier->min_subtotal,
                        'priority' => (int) $tier->priority,
                    ],
                ];
            }
        }

        return $best;
    }

    /**
     * Validasi voucher dan kalkulasi nominal potongannya dari subtotal.
     * Jika valid, buat "held redemption" dan kembalikan id-nya.
     *
     * return sukses:
     * [
     *   'ok' => true,
     *   'amount' => 50000.00,
     *   'voucher' => [...],
     *   'redemption_id' => 123
     * ]
     *
     * return gagal:
     * [
     *   'ok' => false,
     *   'reason' => 'Voucher tidak ditemukan / tidak aktif / dll'
     * ]
     */
    public function validateAndHoldVoucher(string $code, float $subtotal, int $userId, ?string $customerRef = null): array
    {
        $now = now();
        $voucher = Voucher::query()->byCode($code)->activeAt($now)->first();

        if (!$voucher) {
            return ['ok' => false, 'reason' => 'Voucher tidak ditemukan atau tidak aktif.'];
        }

        // Minimal order
        if ($subtotal < (float) $voucher->min_order_total) {
            $kurang = (float) $voucher->min_order_total - $subtotal;
            return ['ok' => false, 'reason' => 'Minimal belanja belum terpenuhi. Tambah belanja Rp ' . number_format($kurang, 0, ',', '.')];
        }

        // Hitung pemakaian total + per user (held & applied dianggap terpakai untuk mencegah race sederhana)
        $usageTotal = VoucherRedemption::where('voucher_id', $voucher->id)
            ->whereIn('status', ['held', 'applied'])
            ->count();

        if (!is_null($voucher->usage_limit_total) && $usageTotal >= (int) $voucher->usage_limit_total) {
            return ['ok' => false, 'reason' => 'Voucher sudah mencapai batas pemakaian.'];
        }

        $usagePerUser = VoucherRedemption::where('voucher_id', $voucher->id)
            ->where('user_id', $userId)
            ->whereIn('status', ['held', 'applied'])
            ->count();

        if (!is_null($voucher->usage_limit_per_user) && $usagePerUser >= (int) $voucher->usage_limit_per_user) {
            return ['ok' => false, 'reason' => 'Batas pemakaian per pengguna telah tercapai.'];
        }

        // Hitung nominal diskon
        $amount = 0.0;
        if ($voucher->type === 'percent') {
            $amount = round($subtotal * ((float) $voucher->value / 100.0));
        } else {
            $amount = (float) $voucher->value;
        }

        if (!is_null($voucher->max_discount_amount)) {
            $amount = min($amount, (float) $voucher->max_discount_amount);
        }

        // Clamp tidak boleh lebih dari subtotal
        $amount = min($amount, $subtotal);

        // Buat held redemption (atomic)
        $redemption = DB::transaction(function () use ($voucher, $userId, $customerRef, $amount) {
            return VoucherRedemption::create([
                'voucher_id' => $voucher->id,
                'user_id' => $userId,
                'order_id' => null,
                'redeemed_at' => now(),
                'customer_ref' => $customerRef,
                'amount_applied' => $amount,
                'status' => 'held',
            ]);
        });

        return [
            'ok' => true,
            'amount' => (float) $amount,
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'type' => $voucher->type,
                'value' => (float) $voucher->value,
                'min_order_total' => (float) $voucher->min_order_total,
                'max_discount_amount' => $voucher->max_discount_amount ? (float) $voucher->max_discount_amount : null,
                'starts_at' => optional($voucher->starts_at)->toIso8601String(),
                'ends_at' => optional($voucher->ends_at)->toIso8601String(),
            ],
            'redemption_id' => $redemption->id,
            'hold_expires_in_minutes' => self::HOLD_TTL_MINUTES,
        ];
    }

    /**
     * Void/batalkan held redemption (mis. user membatalkan voucher sebelum checkout).
     */
    public function voidHeld(int $redemptionId): bool
    {
        $red = VoucherRedemption::find($redemptionId);
        if (!$red)
            return false;
        if ($red->status !== 'held')
            return false;

        // TTL sederhana: boleh di-void kapan saja pada tahap ini
        $red->status = 'void';
        $red->save();
        return true;
    }

    /**
     * Dipakai nanti saat checkout: finalize held -> applied, set order_id + amount_applied final.
     */
    public function applyHeldToOrder(int $redemptionId, int $orderId, float $amountFinal): bool
    {
        $red = VoucherRedemption::find($redemptionId);
        if (!$red || $red->status !== 'held')
            return false;

        $red->status = 'applied';
        $red->order_id = $orderId;
        $red->amount_applied = $amountFinal;
        $red->save();
        return true;
    }
}
