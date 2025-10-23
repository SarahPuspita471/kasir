<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'created_by');
    }

    public function createdDiscountSchemes()
    {
        return $this->hasMany(DiscountScheme::class, 'created_by');
    }

    public function createdVouchers()
    {
        return $this->hasMany(Voucher::class, 'created_by');
    }

    public function voucherRedemptions()
    {
        return $this->hasMany(VoucherRedemption::class);
    }

    public function sales()
    {
        return $this->hasMany(\App\Models\Sale::class);
    }

    // app/Models/Sale.php
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items()
    {
        return $this->hasMany(\App\Models\SaleItem::class);
    }

    public function voucherRedemption() // opsional, jika dipakai
    {
        return $this->hasOne(\App\Models\VoucherRedemption::class, 'order_id');
    }

    // app/Models/SaleItem.php
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }


}
