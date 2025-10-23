<?php

// app/Http/Controllers/User/TaxInfoController.php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TaxService;

class TaxInfoController extends Controller
{
    public function show(TaxService $tax)
    {
        $cur = $tax->current();
        return response()->json([
            'enabled' => (bool)$cur->is_enabled,
            'name'    => $cur->name,
            'rate'    => (float)$cur->rate_percent,
        ]);
    }
}
