<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'customer_name',
        'subtotal',
        'auto_discount',
        'voucher_discount',
        'total',
        'cash_paid',
        'change_due',
        'voucher_redemption_id',
        'voucher_code',
        'discount_snapshot',
        'tax_rate','tax_amount'
    ];

    protected $casts = [
        'discount_snapshot' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getComputedSubtotalAttribute(): int
    {
        $this->loadMissing('items');
        return (int) $this->items->sum(fn($it) => (int) $it->price * (int) $it->qty);
    }

    public function getComputedAutoDiscountAttribute(): int
    {
        $engine = app(\App\Services\DiscountEngine::class);
        return (int) $engine->computeAutoDiscount($this->computed_subtotal);
    }

    public function getComputedVoucherDiscountAttribute(): int
    {
        $this->loadMissing('voucherRedemption');
        return (int) optional($this->voucherRedemption)->amount_applied ?? 0;
    }

    public function getComputedGrandTotalAttribute(): int
    {
        return max(0, $this->computed_subtotal - $this->computed_auto_discount - $this->computed_voucher_discount);
    }

}
