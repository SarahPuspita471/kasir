<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_uid',
        'kode_barang',
        'direction',
        'qty',
        'status',
        'warehouse_payload',
        'warehouse_response',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'warehouse_payload' => 'array',
        'warehouse_response' => 'array',
    ];

    // --- Boot for UUID auto-generate ---
    protected static function booted()
    {
        static::creating(function ($transfer) {
            if (!isset($transfer->transfer_uid)) {
                $transfer->transfer_uid = (string) Str::uuid();
            }
        });
    }

    // --- Relationships ---
    public function product()
    {
        return $this->belongsTo(Product::class, 'kode_barang', 'kode_barang');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // --- Scopes ---
    public function scopeCommitted($query)
    {
        return $query->where('status', 'committed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
