<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\VoucherRedemption;
use App\Services\DiscountEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Services\TaxService;
use App\Models\TaxSetting;

class CheckoutController extends Controller
{
    public function __construct(private DiscountEngine $engine)
    {
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.kode_barang' => ['required', 'string', 'max:100'],
            'items.*.qty' => ['required', 'integer', 'min:1'],

            'voucher.redemption_id' => ['nullable', 'integer', 'min:1'],
            'voucher.code' => ['nullable', 'string', 'max:100'],

            'cash_paid' => ['required', 'integer', 'min:0'],
        ]);

        $userId = $request->user()->id;

        $result = DB::transaction(function () use ($data, $userId) {
            // Ambil produk dengan lock (hindari race stok)
            $kodeList = collect($data['items'])->pluck('kode_barang')->all();
            $products = Product::whereIn('kode_barang', $kodeList)->lockForUpdate()->get()->keyBy('kode_barang');

            $lines = [];
            $subtotal = 0;

            foreach ($data['items'] as $row) {
                $p = $products->get($row['kode_barang']);
                if (!$p)
                    throw new \Exception("Produk {$row['kode_barang']} tidak ditemukan.");
                if (is_null($p->harga_jual))
                    throw new \Exception("Harga belum diatur untuk {$p->nama_barang}.");
                if ($row['qty'] > $p->stok_kasir)
                    throw new \Exception("Stok {$p->nama_barang} tidak cukup.");

                $lineTotal = $p->harga_jual * $row['qty'];
                $subtotal += $lineTotal;

                $lines[] = [
                    'kode_barang' => $p->kode_barang,
                    'nama_barang' => $p->nama_barang,
                    'harga_jual' => $p->harga_jual,
                    'qty' => $row['qty'],
                    'line_total' => $lineTotal,
                    'product' => $p, // ref untuk pengurangan stok
                ];
            }

            // Diskon otomatis
            $auto = $this->engine->computeAutoDiscount($subtotal);

            // Voucher (opsional)
            $voucherAmount = 0;
            $voucherInfo = null;
            $voucherRedemptionId = null;

            if (!empty($data['voucher']['redemption_id'])) {
                $red = VoucherRedemption::lockForUpdate()->find($data['voucher']['redemption_id']);
                if (!$red || $red->status !== 'held') {
                    throw new \Exception('Voucher tidak valid / sudah tidak tersedia.');
                }

                $voucher = $red->voucher; // relasi pada model kamu
                // Rehitung aman (jaga-jaga subtotal berubah)
                if ($subtotal < (float) $voucher->min_order_total) {
                    throw new \Exception('Subtotal tidak memenuhi minimal order untuk voucher.');
                }
                $voucherAmount = $voucher->type === 'percent'
                    ? (int) round($subtotal * ($voucher->value / 100))
                    : (int) $voucher->value;

                if (!is_null($voucher->max_discount_amount)) {
                    $voucherAmount = min($voucherAmount, (int) $voucher->max_discount_amount);
                }
                $voucherAmount = min($voucherAmount, $subtotal);

                $voucherInfo = [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'type' => $voucher->type,
                    'value' => (float) $voucher->value,
                    'min_order_total' => (float) $voucher->min_order_total,
                    'max_discount_amount' => $voucher->max_discount_amount ? (float) $voucher->max_discount_amount : null,
                ];
                $voucherRedemptionId = $red->id;
            }

            // ========== PAJAK: DPP, tax_rate, tax_amount, grand total ==========
            // DPP setelah diskon & voucher
            $dpp = max(0, $subtotal - (int) $auto['amount'] - $voucherAmount);

            // Ambil setting pajak via service (atau fallback langsung TaxSetting jika service belum dibuat)
            $taxSvc = app()->bound(TaxService::class) ? app(TaxService::class) : null;

            if ($taxSvc) {
                $taxCalc = $taxSvc->compute($dpp);     // ['enabled'=>bool,'rate'=>float,'tax'=>int]
                $taxRate = (float) $taxCalc['rate'];
                $taxAmount = (int) $taxCalc['tax'];
            } else {
                // fallback tanpa service
                $cur = TaxSetting::query()->first();
                $rate = ($cur && $cur->is_enabled) ? (float) $cur->rate_percent : 0.0;
                $taxRate = $rate;
                $taxAmount = (int) floor($dpp * ($rate / 100.0));
            }

            $grandTotal = $dpp + $taxAmount;
            // ===================================================================

            if ($data['cash_paid'] < $grandTotal) {
                throw new \Exception('Uang yang diterima kurang dari total.');
            }

            // Buat sale + items + kurangi stok
            $sale = Sale::create([
                'code' => 'TRX-' . now()->format('ymd') . '-' . Str::upper(Str::random(5)),
                'user_id' => $userId,
                'customer_name' => $data['customer_name'] ?? null,

                'subtotal' => $subtotal,
                'auto_discount' => (int) $auto['amount'],
                'voucher_discount' => $voucherAmount,

                // simpan pajak
                'tax_rate' => $taxRate,     // persentase saat transaksi
                'tax_amount' => $taxAmount,   // rupiah pajak

                // total = DPP + pajak
                'total' => $grandTotal,

                'cash_paid' => $data['cash_paid'],
                'change_due' => $data['cash_paid'] - $grandTotal,

                'voucher_redemption_id' => $voucherRedemptionId,
                'voucher_code' => $voucherInfo['code'] ?? null,

                'discount_snapshot' => [
                    'auto' => $auto,
                    'voucher' => $voucherInfo,
                    'tax' => ['rate' => $taxRate, 'amount' => $taxAmount], // optional: untuk audit
                ],
            ]);

            foreach ($lines as $ln) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'kode_barang' => $ln['kode_barang'],
                    'nama_barang' => $ln['nama_barang'],
                    'harga_jual' => $ln['harga_jual'],
                    'qty' => $ln['qty'],
                    'line_total' => $ln['line_total'],
                ]);

                // Kurangi stok kasir
                $ln['product']->decrement('stok_kasir', $ln['qty']);
                // Update status_kasir
                $ln['product']->status_kasir = $ln['product']->stok_kasir > 0 ? 'Tersedia' : 'Tidak Tersedia';
                $ln['product']->save();
            }

            // Finalisasi voucher apabila ada
            if ($voucherRedemptionId) {
                $this->engine->applyHeldToOrder($voucherRedemptionId, $sale->id, $voucherAmount);
            }

            return $sale->fresh(['items', 'user']);
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'sale_id' => $result->id,
                'code' => $result->code,
                'receipt_url' => route('user.sales.receipt', $result),
                'change_due' => $result->change_due,
            ]
        ]);
    }

    // Halaman struk (printable)
    public function receipt(Sale $sale)
    {
        $sale->load('items', 'user');
        return view('user.sales.receipt', compact('sale'));
    }
}
