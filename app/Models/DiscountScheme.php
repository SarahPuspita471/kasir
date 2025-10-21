<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
        'starts_at',
        'ends_at',
        'max_discount_amount',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'max_discount_amount' => 'decimal:2',
    ];

    public function tiers()
    {
        return $this->hasMany(DiscountTier::class)->orderByDesc('priority')->orderByDesc('min_subtotal');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Scope: scheme yang aktif pada waktu tertentu */
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
}
