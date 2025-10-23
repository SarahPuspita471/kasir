{{-- resources/views/admin/tax/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-semibold">Pengaturan Pajak</h1>
    <p class="text-sm text-gray-500">Tarif pajak akan otomatis diterapkan di kasir.</p>
  </div>

  @if(session('status'))
  <div class="p-3 bg-green-50 text-green-800 rounded">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('admin.tax.update') }}" class="max-w-xl space-y-4 bg-white p-5 rounded shadow">
    @csrf

    <div class="flex items-center gap-2">
      <input type="checkbox" id="is_enabled" name="is_enabled" value="1" @checked($setting->is_enabled) class="h-4 w-4">
      <label for="is_enabled" class="text-sm font-medium">Aktifkan Pajak</label>
    </div>

    <div>
      <label class="block text-sm font-medium">Nama Pajak</label>
      <input type="text" name="name" value="{{ old('name', $setting->name) }}" class="w-full border rounded px-3 py-2">
      @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Tarif (%)</label>
      <input type="number" name="rate_percent" step="0.01" min="0" max="100" value="{{ old('rate_percent', $setting->rate_percent) }}" class="w-full border rounded px-3 py-2">
      @error('rate_percent') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div class="flex gap-2">
      <button class="px-4 py-2 rounded bg-indigo-600 text-white">Simpan</button>
    </div>
  </form>
</div>
@endsection
