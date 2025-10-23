@extends('layouts.admin')

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-semibold">Transaksi</h1>
    <p class="text-sm text-gray-500">Daftar transaksi yang dilakukan oleh kasir.</p>
  </div>

  {{-- filter --}}
  <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
    <div>
      <label class="block text-sm font-medium">Dari Tanggal</label>
      <input type="date" name="from" value="{{ request('from') }}" class="w-full border rounded px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">Sampai Tanggal</label>
      <input type="date" name="to" value="{{ request('to') }}" class="w-full border rounded px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium">Kasir</label>
      <select name="cashier_id" class="w-full border rounded px-3 py-2">
        <option value="">Semua</option>
        @foreach($cashiers as $c)
          <option value="{{ $c->id }}" @selected(request('cashier_id')==$c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Cari Kode/Struk</label>
      <input type="text" name="q" value="{{ request('q') }}" placeholder="mis. TRX-251021-XXXX" class="w-full border rounded px-3 py-2">
    </div>
    <div class="flex gap-2">
      <button class="px-4 py-2 rounded bg-indigo-600 text-white">Terapkan</button>
      <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 rounded border">Reset</a>
    </div>
  </form>

  {{-- ringkasan --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
    <div class="p-4 bg-white rounded shadow">
      <div class="text-sm text-gray-500">Total Transaksi</div>
      <div class="text-xl font-semibold">{{ number_format($summary->total_orders ?? 0) }}</div>
    </div>
    <div class="p-4 bg-white rounded shadow">
      <div class="text-sm text-gray-500">Omzet</div>
      <div class="text-xl font-semibold">Rp {{ number_format($summary->revenue ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="p-4 bg-white rounded shadow">
      @php $totalDisc = (int)($summary->auto_sum ?? 0) + (int)($summary->voucher_sum ?? 0); @endphp
      <div class="text-sm text-gray-500">Total Diskon</div>
      <div class="text-xl font-semibold text-red-600">- Rp {{ number_format($totalDisc, 0, ',', '.') }}</div>
    </div>
  </div>

  {{-- tabel --}}
  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left">Waktu</th>
          <th class="px-4 py-2 text-left">Kode</th>
          <th class="px-4 py-2 text-left">Kasir</th>
          <th class="px-4 py-2 text-right">Subtotal</th>
          <th class="px-4 py-2 text-right">Diskon</th>
          <th class="px-4 py-2 text-right">Total</th>
          <th class="px-4 py-2"></th>
        </tr>
      </thead>
      <tbody>
      @forelse($sales as $s)
        @php
          $totalDiscount = (int)($s->calc_auto_discount ?? 0) + (int)($s->calc_voucher_discount ?? 0);
        @endphp
        <tr class="border-t">
          <td class="px-4 py-2">{{ $s->created_at->format('d M Y H:i') }}</td>
          <td class="px-4 py-2 font-medium">{{ $s->code }}</td>
          <td class="px-4 py-2">{{ $s->user->name ?? '-' }}</td>
          <td class="px-4 py-2 text-right">Rp {{ number_format($s->calc_subtotal ?? 0,0,',','.') }}</td>
          <td class="px-4 py-2 text-right text-red-600">- Rp {{ number_format($totalDiscount,0,',','.') }}</td>
          <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($s->calc_grand_total ?? 0,0,',','.') }}</td>
          <td class="px-4 py-2 text-right">
            <a class="text-indigo-600 hover:underline" href="{{ route('admin.transactions.show', $s) }}">Detail</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada transaksi.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div>
    {{ $sales->links() }}
  </div>
</div>
@endsection
