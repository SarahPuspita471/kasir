<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_barang',
        'jenis_barang',
        'unit_barang',
        'stok_kasir',
        'status_kasir',
        'synced_at',
        'harga_jual',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
        'harga_jual' => 'decimal:2'
    ];

    // --- Accessors ---
    public function getStatusKasirAttribute($value)
    {
        return $this->stok_kasir > 0 ? 'Tersedia' : 'Tidak Tersedia';
    }

    // --- Relationships ---
    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'kode_barang', 'kode_barang');
    }
    public function getRouteKeyName(): string
    {
        return 'kode_barang';
    }

}
