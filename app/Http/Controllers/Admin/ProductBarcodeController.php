<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;

class ProductBarcodeController extends Controller
{
    // Preview SVG (inline) -> untuk modal
    public function preview(Product $product)
    {
        // Tipe barcode 1D: Code 128 supaya fleksibel untuk kode alfanumerik
        $svg = app(DNS1D::class)->getBarcodeSVG($product->kode_barang, 'C128', 2, 80, 'black', true);
        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }

    // Download PNG -> simpan sementara ke storage/public/barcodes lalu force download
    public function download(Product $product)
    {
        $pngBase64 = app(DNS1D::class)->getBarcodePNG($product->kode_barang, 'C128', 3, 120, [0,0,0], true);
        $binary = base64_decode($pngBase64);

        $fileName = 'barcode-'.$product->kode_barang.'.png';
        $path = 'barcodes/'.$fileName;

        Storage::disk('public')->put($path, $binary);

        return response()->download(storage_path('app/public/'.$path), $fileName)
            ->deleteFileAfterSend(true);
    }
}
