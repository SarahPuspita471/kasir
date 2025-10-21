<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WarehouseApiService
{
    protected string $base;

    public function __construct()
    {
        $this->base = rtrim(config('services.warehouse.base_url'), '/');
    }

    public function getAllBarang()
    {
        return Http::timeout(5)->retry(2, 500)->get("{$this->base}/barang");
    }

    public function updateStok($kode_barang, $qty, $operation)
    {
        return Http::timeout(5)->retry(2, 500)
            ->put("{$this->base}/barang/{$kode_barang}/stok", [
                'qty' => $qty,
                'operation' => $operation,
            ]);
    }

}
