@extends('layouts.admin')
@section('title','Voucher')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Voucher</h1>
  <a href="{{ route('admin.vouchers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:opacity-90">+ Voucher Baru</a>
</div>

@if(session('ok')) <div class="mb-3 p-3 rounded bg-green-50 text-green-700">{{ session('ok') }}</div> @endif

<form method="get" class="mb-4">
  <div class="flex gap-2">
    <input name="q" value="{{ $q }}" placeholder="Cari kode / deskripsi" class="border rounded px-3 py-2 flex-1">
    <button class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200">Cari</button>
  </div>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
  @foreach($vouchers as $v)
  <div class="border rounded-xl p-4 bg-white">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-xs text-gray-500">Kode</p>
        <p class="text-lg font-semibold tracking-wide">{{ $v->code }}</p>
      </div>
      <span class="text-xs px-2 py-1 rounded-full {{ $v->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
        {{ $v->is_active ? 'Aktif' : 'Nonaktif' }}
      </span>
    </div>

    <p class="mt-2 text-sm text-gray-600">{{ $v->description }}</p>

    <div class="mt-3 text-sm">
      <p>Tipe: <b>{{ $v->type }}</b> ({{ $v->type === 'percent' ? $v->value.'%' : 'Rp '.number_format($v->value,0,',','.') }})</p>
      <p>Min. Order: Rp {{ number_format($v->min_order_total,0,',','.') }}</p>
      <p>Periode: {{ optional($v->starts_at)->format('d M Y H:i') ?? '-' }} — {{ optional($v->ends_at)->format('d M Y H:i') ?? '-' }}</p>
      @if($v->max_discount_amount)
        <p>Plafon: Rp {{ number_format($v->max_discount_amount,0,',','.') }}</p>
      @endif
    </div>

    <div class="mt-4 flex items-center gap-2">
      <a href="{{ route('admin.vouchers.edit',$v) }}" class="px-3 py-1.5 rounded border">Edit</a>
      <form action="{{ route('admin.vouchers.toggle',$v) }}" method="post">@csrf @method('PATCH')
        <button class="px-3 py-1.5 rounded border">{{ $v->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
      </form>
      <form action="{{ route('admin.vouchers.destroy',$v) }}" method="post" onsubmit="return confirm('Hapus voucher?')">
        @csrf @method('DELETE')
        <button class="px-3 py-1.5 rounded border text-red-600">Hapus</button>
      </form>
    </div>
  </div>
  @endforeach
</div>

<div class="mt-4">{{ $vouchers->withQueryString()->links() }}</div>
@endsection
