<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_scheme_id',
        'min_subtotal',
        'type',
        'value',
        'priority',
    ];

    protected $casts = [
        'min_subtotal' => 'decimal:2',
        'value'        => 'decimal:2',
    ];

    public function scheme()
    {
        return $this->belongsTo(DiscountScheme::class, 'discount_scheme_id');
    }
}
