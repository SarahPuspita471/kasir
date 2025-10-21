<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'min_order_total',
        'starts_at',
        'ends_at',
        'usage_limit_total',
        'usage_limit_per_user',
        'max_discount_amount',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'value'               => 'decimal:2',
        'min_order_total'     => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'is_active'           => 'boolean',
        'starts_at'           => 'datetime',
        'ends_at'             => 'datetime',
    ];

    // Selalu simpan code uppercase agar pencarian konsisten
    public function setCodeAttribute($v)
    {
        $this->attributes['code'] = mb_strtoupper(trim($v));
    }

    public function redemptions()
    {
        return $this->hasMany(VoucherRedemption::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Scope: aktif & dalam periode */
    public function scopeActiveAt($q, $at = null)
    {
        $at = $at ?? now();
        return $q->where('is_active', true)
                 ->where(function ($qq) use ($at) {
                     $qq->whereNull('starts_at')->orWhere('starts_at', '<=', $at);
                 })
                 ->where(function ($qq) use ($at) {
                     $qq->whereNull('ends_at')->orWhere('ends_at', '>=', $at);
                 });
    }

    /** Cari dengan code (case-insensitive) */
    public function scopeByCode($q, string $code)
    {
        return $q->where('code', mb_strtoupper(trim($code)));
    }
}
