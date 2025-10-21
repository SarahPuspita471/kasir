<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDiscountSchemeRequest;
use App\Http\Requests\Admin\UpdateDiscountSchemeRequest;
use App\Models\DiscountScheme;
use Illuminate\Http\Request;

class DiscountSchemeController extends Controller
{
    public function index()
    {
        $schemes = DiscountScheme::with('tiers')->orderByDesc('created_at')->get();
        return view('admin.discounts.index', compact('schemes'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(StoreDiscountSchemeRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        DiscountScheme::create($data);
        return redirect()->route('admin.discounts.index')->with('ok','Scheme dibuat.');
    }

    public function edit(DiscountScheme $scheme)
    {
        $scheme->load('tiers');
        return view('admin.discounts.edit', compact('scheme'));
    }

    public function update(UpdateDiscountSchemeRequest $request, DiscountScheme $scheme)
    {
        $scheme->update($request->validated());
        return redirect()->route('admin.discounts.index')->with('ok','Scheme diperbarui.');
    }

    public function destroy(DiscountScheme $scheme)
    {
        $scheme->delete();
        return back()->with('ok','Scheme dihapus.');
    }
}
