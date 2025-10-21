<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Services\WarehouseApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferController extends Controller
{
    public function __construct(protected WarehouseApiService $warehouse)
    {
    }

    public function ambil(Request $request)
    {
        $data = $request->validate([
            'kode_barang' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $transfer = StockTransfer::create([
                'transfer_uid' => Str::uuid(),
                'kode_barang' => $data['kode_barang'],
                'direction' => 'inbound_from_warehouse',
                'qty' => $data['qty'],
                'created_by' => $request->user()->id,
                'status' => 'pending',
            ]);

            $payload = ['qty' => $data['qty'], 'operation' => 'kurangi'];
            $res = $this->warehouse->updateStok($data['kode_barang'], $data['qty'], 'kurangi');
            $transfer->warehouse_payload = $payload;

            if ($res->successful() && $res->json('status') === 'success') {
                $transfer->warehouse_response = $res->json();
                $transfer->status = 'committed';

                $barang = $res->json('data'); // ambil data barang dari gudang

                Product::updateOrCreate(
                    ['kode_barang' => $barang['kode_barang']],
                    [
                        'nama_barang' => $barang['nama_barang'] ?? 'Tanpa Nama',
                        'kategori_barang' => $barang['kategori_barang'] ?? null,
                        'jenis_barang' => $barang['jenis_barang'] ?? null,
                        'unit_barang' => $barang['unit_barang'] ?? null,
                        'stok_kasir' => DB::raw("stok_kasir + {$data['qty']}"),
                        'status_kasir' => 'Tersedia',
                    ]
                );
            } else {
                $transfer->status = 'failed';
                $transfer->warehouse_response = $res->json();
            }
            $transfer->save();

            return response()->json([
                'status' => $transfer->status,
                'message' => $transfer->status === 'committed'
                    ? 'Berhasil ambil stok dari gudang'
                    : 'Gagal ambil stok dari gudang',
                'data' => $transfer
            ]);
        });
    }

    public function index()
    {
        $produk = Product::orderBy('nama_barang')->get();
        return view('admin.stock.index', compact('produk'));
    }

}

