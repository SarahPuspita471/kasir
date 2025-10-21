<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SetPriceRequest;
use App\Models\Product;

class ProductPriceController extends Controller
{
    public function update(SetPriceRequest $request)
    {
        $product = Product::where('kode_barang', $request->kode_barang)->first();

        if (! $product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk belum ada di kasir. Ambil stok dulu dari gudang.',
            ], 404);
        }

        $product->harga_jual = $request->harga_jual;
        $product->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Harga jual berhasil diperbarui.',
            'data'    => [
                'kode_barang' => $product->kode_barang,
                'harga_jual'  => $product->harga_jual,
            ]
        ]);
    }
}
