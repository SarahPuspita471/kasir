@php
  $v = $voucher ?? null;
@endphp

@if ($errors->any())
  <div class="mb-3 p-3 rounded bg-red-50 text-red-700">
    <ul class="list-disc ml-5">
      @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form method="post" action="{{ $action }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-xl border">
  @csrf
  @method($method)

  <div class="md:col-span-1">
    <label class="text-sm text-gray-600">Kode</label>
    <input name="code" value="{{ old('code', $v->code ?? '') }}" class="mt-1 w-full border rounded px-3 py-2 uppercase" required>
  </div>

  <div class="md:col-span-1">
    <label class="text-sm text-gray-600">Deskripsi</label>
    <input name="description" value="{{ old('description', $v->description ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>

  <div>
    <label class="text-sm text-gray-600">Tipe</label>
    <select name="type" class="mt-1 w-full border rounded px-3 py-2">
      <option value="percent" {{ old('type', $v->type ?? '')==='percent'?'selected':'' }}>Persentase (%)</option>
      <option value="amount"  {{ old('type', $v->type ?? '')==='amount'?'selected':''  }}>Nominal (Rp)</option>
    </select>
  </div>

  <div>
    <label class="text-sm text-gray-600">Nilai</label>
    <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $v->value ?? 0) }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>

  <div>
    <label class="text-sm text-gray-600">Minimal Order (Rp)</label>
    <input type="number" min="0" name="min_order_total" value="{{ old('min_order_total', $v->min_order_total ?? 0) }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>

  <div>
    <label class="text-sm text-gray-600">Plafon Diskon (Rp) (opsional)</label>
    <input type="number" min="0" name="max_discount_amount" value="{{ old('max_discount_amount', $v->max_discount_amount ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>

  <div>
    <label class="text-sm text-gray-600">Mulai</label>
    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($v->starts_at ?? null)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>

  <div>
    <label class="text-sm text-gray-600">Selesai</label>
    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($v->ends_at ?? null)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>

  <div>
    <label class="text-sm text-gray-600">Limit Pemakaian Total (opsional)</label>
    <input type="number" min="1" name="usage_limit_total" value="{{ old('usage_limit_total', $v->usage_limit_total ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>

  <div>
    <label class="text-sm text-gray-600">Limit per Pengguna (opsional)</label>
    <input type="number" min="1" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', $v->usage_limit_per_user ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
  </div>

  <div class="md:col-span-2 flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" id="is_active" class="rounded border-gray-300" {{ old('is_active', $v->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active">Aktif</label>
  </div>

  <div class="md:col-span-2 flex justify-end gap-2">
    <a href="{{ route('admin.vouchers.index') }}" class="px-4 py-2 rounded border">Batal</a>
    <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:opacity-90">Simpan</button>
  </div>
</form>
