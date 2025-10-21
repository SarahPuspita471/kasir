<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'code','user_id','customer_name','subtotal','auto_discount','voucher_discount',
        'total','cash_paid','change_due','voucher_redemption_id','voucher_code','discount_snapshot'
    ];

    protected $casts = [
        'discount_snapshot' => 'array',
    ];

    public function items() { return $this->hasMany(SaleItem::class); }
    public function user()  { return $this->belongsTo(User::class); }
}
