<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVoucherRequest;
use App\Http\Requests\Admin\UpdateVoucherRequest;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $vouchers = Voucher::query()
            ->when($q !== '', fn($s)=> $s->where('code','like',"%{$q}%")->orWhere('description','like',"%{$q}%"))
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('admin.vouchers.index', compact('vouchers','q'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(StoreVoucherRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        Voucher::create($data);
        return redirect()->route('admin.vouchers.index')->with('ok','Voucher berhasil dibuat.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(UpdateVoucherRequest $request, Voucher $voucher)
    {
        $data = $request->validated();
        $voucher->update($data);
        return redirect()->route('admin.vouchers.index')->with('ok','Voucher diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('ok','Voucher dihapus.');
    }

    public function toggle(Voucher $voucher)
    {
        $voucher->is_active = ! $voucher->is_active;
        $voucher->save();
        return back()->with('ok','Status voucher diperbarui.');
    }
}
