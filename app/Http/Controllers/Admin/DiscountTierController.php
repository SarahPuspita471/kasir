<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDiscountTierRequest;
use App\Http\Requests\Admin\UpdateDiscountTierRequest;
use App\Models\DiscountScheme;
use App\Models\DiscountTier;

class DiscountTierController extends Controller
{
    public function store(StoreDiscountTierRequest $request, DiscountScheme $scheme)
    {
        $data = $request->validated();
        $data['discount_scheme_id'] = $scheme->id;
        $data['priority'] = $data['priority'] ?? 0;
        DiscountTier::create($data);
        return back()->with('ok','Tier ditambahkan.');
    }

    public function update(UpdateDiscountTierRequest $request, DiscountTier $tier)
    {
        $tier->update($request->validated());
        return back()->with('ok','Tier diperbarui.');
    }

    public function destroy(DiscountTier $tier)
    {
        $tier->delete();
        return back()->with('ok','Tier dihapus.');
    }
}
