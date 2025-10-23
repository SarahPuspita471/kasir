<?php 
// app/Services/TaxService.php
namespace App\Services;

use App\Models\TaxSetting;

class TaxService
{
    public function current(): TaxSetting
    {
        return TaxSetting::query()->first(); // single row
    }

    public function compute(int $dpp): array
    {
        $setting = $this->current();
        $rate = $setting?->is_enabled ? (float)$setting->rate_percent : 0.0;
        $tax  = (int) floor($dpp * ($rate / 100.0)); // rupiah, dibulatkan ke bawah
        return ['enabled' => (bool)$setting?->is_enabled, 'rate' => $rate, 'tax' => $tax];
    }
}
