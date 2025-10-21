@extends('layouts.admin')
@section('title','Edit Scheme')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Edit Scheme</h1>
@include('admin.discounts._form', ['action'=>route('admin.discounts.update',$scheme),'method'=>'PUT','scheme'=>$scheme])

{{-- Kelola Tiers --}}
<div class="mt-6 border rounded-xl p-4 bg-white">
  @if(session('ok')) <div class="mb-3 p-3 rounded bg-green-50 text-green-700">{{ session('ok') }}</div> @endif
  <h2 class="text-lg font-semibold">Tiers</h2>

  {{-- Form tambah tier --}}
  <form class="mt-3 grid grid-cols-1 md:grid-cols-5 gap-3" method="post" action="{{ route('admin.tiers.store',$scheme) }}">
    @csrf
    <div>
      <label class="text-sm text-gray-600">Min Subtotal</label>
      <input type="number" name="min_subtotal" min="0" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>
    <div>
      <label class="text-sm text-gray-600">Tipe</label>
      <select name="type" class="mt-1 w-full border rounded px-3 py-2">
        <option value="percent">Persentase</option>
        <option value="amount">Nominal</option>
      </select>
    </div>
    <div>
      <label class="text-sm text-gray-600">Nilai</label>
      <input type="number" name="value" step="0.01" min="0" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>
    <div>
      <label class="text-sm text-gray-600">Priority</label>
      <input type="number" name="priority" min="0" value="0" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div class="flex items-end">
      <button class="w-full px-4 py-2 rounded bg-indigo-600 text-white hover:opacity-90">Tambah</button>
    </div>
  </form>

  {{-- List tiers --}}
  <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
    @forelse($scheme->tiers as $t)
    <form class="border rounded-lg p-3 bg-gray-50" method="post" action="{{ route('admin.tiers.update',$t) }}">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-2 text-sm">
        <div>
          <label class="text-gray-600">Min Subtotal</label>
          <input type="number" name="min_subtotal" value="{{ $t->min_subtotal }}" class="mt-1 w-full border rounded px-2 py-1">
        </div>
        <div>
          <label class="text-gray-600">Priority</label>
          <input type="number" name="priority" value="{{ $t->priority }}" class="mt-1 w-full border rounded px-2 py-1">
        </div>
        <div>
          <label class="text-gray-600">Tipe</label>
          <select name="type" class="mt-1 w-full border rounded px-2 py-1">
            <option value="percent" {{ $t->type==='percent'?'selected':'' }}>Persentase</option>
            <option value="amount"  {{ $t->type==='amount'?'selected':'' }}>Nominal</option>
          </select>
        </div>
        <div>
          <label class="text-gray-600">Nilai</label>
          <input type="number" step="0.01" name="value" value="{{ $t->value }}" class="mt-1 w-full border rounded px-2 py-1">
        </div>
      </div>
      <div class="mt-3 flex items-center justify-between">
        <button class="px-3 py-1.5 rounded border bg-white">Simpan</button>
    </form>
        <form method="post" action="{{ route('admin.tiers.destroy',$t) }}" onsubmit="return confirm('Hapus tier?')">
          @csrf @method('DELETE')
          <button class="px-3 py-1.5 rounded border text-red-600">Hapus</button>
        </form>
      </div>
    @empty
      <p class="text-sm text-gray-500">Belum ada tier.</p>
    @endforelse
  </div>
</div>
@endsection
