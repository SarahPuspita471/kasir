@extends('layouts.admin')
@section('title','Discount Schemes')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Discount Schemes</h1>
  <a href="{{ route('admin.discounts.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:opacity-90">+ Scheme Baru</a>
</div>

@if(session('ok')) <div class="mb-3 p-3 rounded bg-green-50 text-green-700">{{ session('ok') }}</div> @endif

<div class="space-y-4">
@foreach($schemes as $s)
  <div class="border rounded-xl p-4 bg-white">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="text-lg font-semibold">{{ $s->name }}</p>
        <p class="text-sm text-gray-600">
          Status:
          <span class="px-2 py-0.5 rounded-full text-xs {{ $s->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
            {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
          </span>
        </p>
        <p class="text-sm text-gray-600">Periode: {{ optional($s->starts_at)->format('d M Y H:i') ?? '-' }} — {{ optional($s->ends_at)->format('d M Y H:i') ?? '-' }}</p>
        @if($s->max_discount_amount)
          <p class="text-sm">Plafon: Rp {{ number_format($s->max_discount_amount,0,',','.') }}</p>
        @endif
      </div>
      <a href="{{ route('admin.discounts.edit',$s) }}" class="px-3 py-1.5 rounded border h-9">Kelola</a>
    </div>

    <div class="mt-3">
      <p class="text-sm font-medium">Tiers</p>
      <div class="mt-2 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
        @forelse($s->tiers as $t)
          <div class="border rounded-lg p-3">
            <p class="text-sm">Min Subtotal: <b>Rp {{ number_format($t->min_subtotal,0,',','.') }}</b></p>
            <p class="text-sm">Diskon: <b>{{ $t->type==='percent' ? $t->value.'%' : 'Rp '.number_format($t->value,0,',','.') }}</b></p>
            <p class="text-sm">Priority: {{ $t->priority }}</p>
          </div>
        @empty
          <p class="text-sm text-gray-500">Belum ada tier.</p>
        @endforelse
      </div>
    </div>
  </div>
@endforeach
</div>
@endsection
