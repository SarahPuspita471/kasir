<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        return view('user.pos.index');
    }

    // JSON untuk grid produk (UI)
    public function products(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = Product::query()
            ->where('stok_kasir', '>', 0)
            ->orderBy('nama_barang');

        if ($q !== '') {
            $query->where(function ($s) use ($q) {
                $s->where('kode_barang', 'like', "%{$q}%")
                  ->orWhere('nama_barang', 'like', "%{$q}%");
            });
        }

        // batasi field agar ringan
        $items = $query->limit(60)->get([
            'kode_barang', 'nama_barang', 'stok_kasir', 'harga_jual'
        ])->map(function ($p) {
            return [
                'kode_barang' => $p->kode_barang,
                'nama_barang' => $p->nama_barang,
                'stok_kasir'  => (int) $p->stok_kasir,
                'harga_jual'  => $p->harga_jual ? (float) $p->harga_jual : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $items,
        ]);
    }
}
