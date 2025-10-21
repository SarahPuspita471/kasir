@extends('layouts.admin')
@section('title', 'Kelola Stok | Kasirku')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">

  {{-- Panel Stok Gudang --}}
  <div class="flex-1 card p-6 anim-card-in">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-semibold">📦 Stok Gudang</h2>
      <button id="refreshGudang" class="px-3 py-1.5 rounded-md bg-indigo-600 text-white text-sm hover:opacity-90">
        Refresh
      </button>
    </div>

    <div id="tabelGudang" class="overflow-x-auto text-sm">
      <p class="text-gray-500 text-center py-10">Memuat data...</p>
    </div>
  </div>

  {{-- Panel Stok Kasir --}}
  <div class="flex-1 card p-6 anim-card-in anim-delay-1">
    <h2 class="text-xl font-semibold mb-4">🏪 Stok Kasir</h2>
    <div id="tabelKasir" class="overflow-x-auto text-sm">
      @if($produk->isEmpty())
        <p class="text-gray-500 text-center py-10">Belum ada stok lokal.</p>
      @else
        <table class="min-w-full border-collapse">
          <thead>
            <tr class="bg-gray-100 text-left text-gray-600 text-xs uppercase tracking-wide">
              <th class="px-3 py-2">Kode</th>
              <th class="px-3 py-2">Nama</th>
              <th class="px-3 py-2">Stok</th>
              <th class="px-3 py-2">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($produk as $item)
            <tr class="border-b hover:bg-gray-50 transition">
              <td class="px-3 py-2">{{ $item->kode_barang }}</td>
              <td class="px-3 py-2">{{ $item->nama_barang }}</td>
              <td class="px-3 py-2">{{ $item->stok_kasir }}</td>
              <td class="px-3 py-2">
                <span class="text-xs px-2 py-1 rounded-full 
                  {{ $item->stok_kasir > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                  {{ $item->status_kasir }}
                </span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>
</div>

{{-- Modal ambil stok --}}
<div id="modalAmbil" class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
    <h3 class="text-lg font-semibold mb-2">Ambil dari Gudang</h3>
    <p id="modalKode" class="text-gray-600 mb-3"></p>
    <form id="formAmbil" class="space-y-3">
      <input type="hidden" name="kode_barang" id="kodeBarang">
      <div>
        <label class="text-sm text-gray-600">Jumlah</label>
        <input type="number" name="qty" min="1" class="w-full mt-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
      </div>
      <div class="flex justify-end gap-2 pt-3">
        <button type="button" id="tutupModal" class="px-3 py-1.5 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
        <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:opacity-90">Ambil</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('modalAmbil');
  const kodeInput = document.getElementById('kodeBarang');
  const kodeLabel = document.getElementById('modalKode');
  const refreshGudang = document.getElementById('refreshGudang');
  const tabelGudang = document.getElementById('tabelGudang');
  const tutupModal = document.getElementById('tutupModal');
  const formAmbil = document.getElementById('formAmbil');

  // --- Fetch data gudang ---
  const loadGudang = async () => {
    tabelGudang.innerHTML = '<p class="text-gray-500 text-center py-10">Memuat data...</p>';
    try {
      const res = await fetch("{{ config('services.warehouse.base_url') }}/barang");
      const json = await res.json();
      if (json.status === 'success') {
        let rows = `
          <table class="min-w-full border-collapse">
            <thead>
              <tr class="bg-gray-100 text-left text-gray-600 text-xs uppercase tracking-wide">
                <th class="px-3 py-2">Kode</th>
                <th class="px-3 py-2">Nama</th>
                <th class="px-3 py-2">Stok</th>
                <th class="px-3 py-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
        `;
        json.data.forEach(b => {
          rows += `
            <tr class="border-b hover:bg-gray-50 transition">
              <td class="px-3 py-2">${b.kode_barang}</td>
              <td class="px-3 py-2">${b.nama_barang}</td>
              <td class="px-3 py-2">${b.stok_barang}</td>
              <td class="px-3 py-2">
                <button 
                  class="ambil px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:opacity-90" 
                  data-kode="${b.kode_barang}" 
                  data-nama="${b.nama_barang}">
                  Ambil
                </button>
              </td>
            </tr>`;
        });
        tabelGudang.innerHTML = rows + '</tbody></table>';
      } else {
        tabelGudang.innerHTML = `<p class="text-red-500 text-center py-10">Gagal memuat data.</p>`;
      }
    } catch (e) {
      tabelGudang.innerHTML = `<p class="text-red-500 text-center py-10">Tidak dapat terhubung ke API Gudang.</p>`;
    }
  };

  refreshGudang.addEventListener('click', loadGudang);
  loadGudang();

  // --- Buka modal ambil ---
  document.addEventListener('click', e => {
    if (e.target.classList.contains('ambil')) {
      const kode = e.target.dataset.kode;
      const nama = e.target.dataset.nama;
      kodeInput.value = kode;
      kodeLabel.textContent = `Ambil stok untuk ${nama} (${kode})`;
      modal.classList.remove('hidden');
    }
  });

  // --- Tutup modal ---
  tutupModal.addEventListener('click', () => modal.classList.add('hidden'));

  // --- Submit form ambil stok ---
  formAmbil.addEventListener('submit', async e => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(formAmbil));
    const res = await fetch('{{ route('admin.stock.ambil') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(data)
    });
    const json = await res.json();
    alert(json.message);
    modal.classList.add('hidden');
    loadGudang();
  });
});
</script>
@endpush
