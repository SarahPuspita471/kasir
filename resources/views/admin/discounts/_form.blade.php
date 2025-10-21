@php $s = $scheme ?? null; @endphp
@if ($errors->any())
  <div class="mb-3 p-3 rounded bg-red-50 text-red-700">
    <ul class="list-disc ml-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<form method="post" action="{{ $action }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-xl border">
  @csrf @method($method)
  <div>
    <label class="text-sm text-gray-600">Nama</label>
    <input name="name" value="{{ old('name', $s->name ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
  </div>
  <div>
    <label class="text-sm text-gray-600">Plafon Diskon (Rp) (opsional)</label>
    <input type="number" name="max_discount_amount" min="0" value="{{ old('max_discount_amount', $s->max_discount_amount ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>
  <div>
    <label class="text-sm text-gray-600">Mulai</label>
    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($s->starts_at ?? null)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>
  <div>
    <label class="text-sm text-gray-600">Selesai</label>
    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($s->ends_at ?? null)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>
  <div class="md:col-span-2 flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" id="is_active" class="rounded border-gray-300" {{ old('is_active', $s->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active">Aktif</label>
  </div>
  <div class="md:col-span-2 flex justify-end gap-2">
    <a href="{{ route('admin.discounts.index') }}" class="px-4 py-2 rounded border">Batal</a>
    <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:opacity-90">Simpan</button>
  </div>
</form>
