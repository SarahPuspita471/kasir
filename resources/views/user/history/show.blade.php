@extends('layouts.user')

@section('content')
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold">Detail Transaksi</h1>
        <p class="text-sm text-gray-500">Kode: {{ $sale->code }} • {{ $sale->created_at->format('d M Y H:i') }}</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('user.history.index') }}" class="px-4 py-2 border rounded">Kembali</a>
        <a href="{{ url('/user/sales/' . $sale->id . '/receipt') }}" class="px-4 py-2 bg-indigo-600 text-white rounded"
          target="_blank">Lihat Struk</a>
      </div>
    </div>

    @php
      $subtotal = (int) ($sale->calc_subtotal ?? 0);
      $auto = (int) ($sale->calc_auto_discount ?? 0);
      $vou = (int) ($sale->calc_voucher_discount ?? 0);
      $grand = (int) ($sale->calc_grand_total ?? 0);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <div class="p-4 bg-white rounded shadow">
        <div class="text-sm text-gray-500">Subtotal</div>
        <div class="text-xl font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
      </div>
      <div class="p-4 bg-white rounded shadow">
        <div class="text-sm text-gray-500">Diskon Otomatis</div>
        <div class="text-xl font-semibold text-red-600">- Rp {{ number_format($auto, 0, ',', '.') }}</div>
      </div>
      <div class="p-4 bg-white rounded shadow">
        <div class="text-sm text-gray-500">Diskon Voucher</div>
        <div class="text-xl font-semibold text-red-600">- Rp {{ number_format($vou, 0, ',', '.') }}</div>
      </div>
      <div class="p-4 bg-white rounded shadow">
        <div class="text-sm text-gray-500">Grand Total</div>
        <div class="text-xl font-semibold">Rp {{ number_format($grand, 0, ',', '.') }}</div>
      </div>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left">Produk</th>
            <th class="px-4 py-2 text-right">Qty</th>
            <th class="px-4 py-2 text-right">Harga</th>
            <th class="px-4 py-2 text-right">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sale->items as $it)
            <tr class="border-t">
              <td class="px-4 py-2">
                <div class="font-medium">{{ $it->nama_barang }}</div>
                <div class="text-xs text-gray-500">Kode: {{ $it->kode_barang }}</div>
              </td>
              <td class="px-4 py-2 text-right">{{ number_format($it->qty) }}</td>
              <td class="px-4 py-2 text-right">Rp {{ number_format($it->harga_jual, 0, ',', '.') }}</td>
              <td class="px-4 py-2 text-right">Rp {{ number_format($it->line_total, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
          <tr>
            <td colspan="3" class="px-4 py-2 text-right font-medium">Subtotal</td>
            <td class="px-4 py-2 text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td colspan="3" class="px-4 py-2 text-right font-medium">Diskon Otomatis</td>
            <td class="px-4 py-2 text-right text-red-600">- Rp {{ number_format($auto, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td colspan="3" class="px-4 py-2 text-right font-medium">Diskon Voucher</td>
            <td class="px-4 py-2 text-right text-red-600">- Rp {{ number_format($vou, 0, ',', '.') }}</td>
          </tr>
          <div class="card p-4">
            <div class="text-sm text-gray-500">Pajak
              ({{ rtrim(rtrim(number_format($sale->tax_rate, 2, '.', ''), '0'), '.') }}%)</div>
            <div class="mt-1 text-lg font-semibold">
              Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}
            </div>
          </div>
          <tr>
            <td colspan="3" class="px-4 py-2 text-right font-semibold">Grand Total</td>
            <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($grand, 0, ',', '.') }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
@endsection