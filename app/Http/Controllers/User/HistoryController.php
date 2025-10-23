<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date'],
            'q'    => ['nullable', 'string', 'max:50'],
        ]);

        $userId = $request->user()->id;

        $q = Sale::query()
            ->with('items') // untuk show ringkas kalau perlu
            ->where('user_id', $userId) // ❗ hanya milik kasir ini
            ->when($request->filled('from'), fn($qq) => $qq->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn($qq) => $qq->whereDate('created_at', '<=', $request->date('to')))
            ->when($request->filled('q'), fn($qq) => $qq->where('code', 'like', '%'.$request->q.'%'))
            ->latest('created_at');

        // ringkasan sederhana untuk user aktif
        $summary = (clone $q)
            ->selectRaw('COUNT(*) as total_orders, SUM(total) as revenue, SUM(auto_discount) as auto_sum, SUM(voucher_discount) as voucher_sum')
            ->first();

        $sales = $q->paginate(15)->withQueryString();

        // properti kalkulasi (langsung dari kolom sales)
        foreach ($sales as $sale) {
            $sale->calc_subtotal         = (int) $sale->subtotal;
            $sale->calc_auto_discount    = (int) $sale->auto_discount;
            $sale->calc_voucher_discount = (int) $sale->voucher_discount;
            $sale->calc_grand_total      = (int) $sale->total;
        }

        return view('user/history/index', compact('sales', 'summary'));
    }

    public function show(Request $request, Sale $sale)
    {
        // ❗ cegah akses transaksi milik orang lain
        if ($sale->user_id !== $request->user()->id) {
            abort(403);
        }

        $sale->load('items');

        $sale->calc_subtotal         = (int) $sale->subtotal;
        $sale->calc_auto_discount    = (int) $sale->auto_discount;
        $sale->calc_voucher_discount = (int) $sale->voucher_discount;
        $sale->calc_grand_total      = (int) $sale->total;

        return view('user/history/show', compact('sale'));
    }
}
