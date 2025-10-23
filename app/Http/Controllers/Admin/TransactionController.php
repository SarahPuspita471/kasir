<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'cashier_id' => ['nullable', 'integer', 'exists:users,id'],
            'q' => ['nullable', 'string', 'max:50'],
        ]);

        $q = Sale::query()
            ->with(['user', 'items'])
            ->when($request->filled('from'), fn($qq) => $qq->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn($qq) => $qq->whereDate('created_at', '<=', $request->date('to')))
            ->when($request->filled('cashier_id'), fn($qq) => $qq->where('user_id', $request->integer('cashier_id')))
            ->when($request->filled('q'), fn($qq) => $qq->where('code', 'like', '%' . $request->q . '%'))
            ->latest('created_at');

        // ringkasan pakai kolom yang ada di tabel sales
        $summary = (clone $q)
            ->selectRaw('COUNT(*) as total_orders, SUM(total) as revenue, SUM(auto_discount) as auto_sum, SUM(voucher_discount) as voucher_sum')
            ->first();

        $sales = $q->paginate(15)->withQueryString();

        // set properti kalkulasi untuk view (langsung dari kolom sales)
        foreach ($sales as $sale) {
            $sale->calc_subtotal = (int) $sale->subtotal;
            $sale->calc_auto_discount = (int) $sale->auto_discount;
            $sale->calc_voucher_discount = (int) $sale->voucher_discount;
            $sale->calc_grand_total = (int) $sale->total;
        }

        $cashiers = User::query()->where('role', 'user')->orderBy('name')->get(['id', 'name']);

        return view('admin/transactions/index', [
            'sales' => $sales,
            'cashiers' => $cashiers,
            'summary' => $summary,
        ]);
    }

    public function show(Sale $sale)
    {
        // cukup user + items; tidak ada relasi lain yang wajib
        $sale->load(['user', 'items']);

        // angka ambil dari tabel sales
        $sale->calc_subtotal = (int) $sale->subtotal;
        $sale->calc_auto_discount = (int) $sale->auto_discount;
        $sale->calc_voucher_discount = (int) $sale->voucher_discount;
        $sale->calc_grand_total = (int) $sale->total;

        return view('admin/transactions/show', compact('sale'));
    }
}
