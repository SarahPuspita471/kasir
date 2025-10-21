<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id','kode_barang','nama_barang','harga_jual','qty','line_total'
    ];

    public function sale() { return $this->belongsTo(Sale::class); }
}
