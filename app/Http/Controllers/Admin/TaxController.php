<?php

// app/Http/Controllers/Admin/TaxController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxSetting;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function edit()
    {
        $setting = TaxSetting::first();
        return view('admin/tax/edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'is_enabled'   => ['nullable','boolean'],
            'rate_percent' => ['required','numeric','min:0','max:100'],
            'name'         => ['required','string','max:50'],
        ]);

        $setting = TaxSetting::first();
        $setting->update([
            'is_enabled'   => (bool) ($data['is_enabled'] ?? false),
            'rate_percent' => $data['rate_percent'],
            'name'         => $data['name'],
        ]);

        return redirect()->route('admin.tax.edit')->with('status','Pengaturan pajak disimpan.');
    }
}

