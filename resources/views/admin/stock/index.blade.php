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
                                <th class="px-3 py-2">Harga Jual</th> {{-- NEW --}}
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Aksi</th> {{-- NEW --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produk as $item)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-3 py-2">{{ $item->kode_barang }}</td>
                                    <td class="px-3 py-2">{{ $item->nama_barang }}</td>
                                    <td class="px-3 py-2">{{ $item->stok_kasir }}</td>

                                    <td class="px-3 py-2">
                                        {{ $item->harga_jual !== null ? 'Rp ' . number_format($item->harga_jual, 0, ',', '.') : '-' }}
                                    </td>

                                    <td class="px-3 py-2">
                                        <span
                                            class="text-xs px-2 py-1 rounded-full 
                                                                                                          {{ $item->stok_kasir > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $item->status_kasir }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-2 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Atur Harga (punyamu sudah ada) --}}
                                            <button
                                                class="btn-set-price px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:opacity-90"
                                                data-kode="{{ $item->kode_barang }}" data-nama="{{ $item->nama_barang }}"
                                                data-harga="{{ $item->harga_jual ?? '' }}">
                                                Atur Harga
                                            </button>

                                            {{-- Barcode: kirim URL jadi data-attr, biar tidak bingung id/kode --}}
                                            <button
                                                class="btn-barcode px-3 py-1.5 bg-gray-100 text-gray-800 text-xs rounded-md hover:bg-gray-200"
                                                data-preview="{{ route('admin.products.barcode.preview', $item) }}"
                                                data-download="{{ route('admin.products.barcode.download', $item) }}"
                                                data-kode="{{ $item->kode_barang }}" data-nama="{{ $item->nama_barang }}">
                                                Barcode
                                            </button>

                                            <a href="{{ route('admin.products.barcode.download', $item) }}"
                                                class="px-3 py-1.5 bg-white border text-xs rounded-md hover:bg-gray-50">
                                                Download
                                            </a>
                                        </div>
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
                    <input type="number" name="qty" min="1"
                        class="w-full mt-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" id="tutupModal"
                        class="px-3 py-1.5 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit"
                        class="px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:opacity-90">Ambil</button>
                </div>
            </form>
        </div>
    </div>


    {{-- Modal Atur Harga --}}
    <div id="modalSetPrice" class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-2">Atur Harga Jual</h3>
            <p id="priceInfo" class="text-gray-600 mb-3"></p>
            <form id="formSetPrice" class="space-y-3">
                @csrf
                <input type="hidden" name="kode_barang" id="priceKode">

                <div>
                    <label class="text-sm text-gray-600">Harga Jual (Rp)</label>
                    <input type="number" name="harga_jual" id="priceValue" min="0" step="100"
                        class="w-full mt-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>

                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" id="priceCancel"
                        class="px-3 py-1.5 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                    <button type="submit"
                        class="px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:opacity-90">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Preview Barcode --}}
    <div id="modalBarcode" class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Preview Barcode</h3>
                    <p id="bcInfo" class="text-sm text-gray-600"></p>
                </div>
                <button id="bcClose" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <div class="mt-4 border rounded-lg p-4 bg-gray-50">
                <div id="bcPreview" class="flex items-center justify-center min-h-32">
                    <p class="text-gray-500">Memuat...</p>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <a id="bcDownload" href="#" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:opacity-90">
                    Download PNG
                </a>
            </div>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modalSetPrice');
            const priceInfo = document.getElementById('priceInfo');
            const priceKode = document.getElementById('priceKode');
            const priceValue = document.getElementById('priceValue');
            const priceCancel = document.getElementById('priceCancel');
            const formSetPrice = document.getElementById('formSetPrice');

            // Buka modal
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-set-price')) {
                    const kode = e.target.dataset.kode;
                    const nama = e.target.dataset.nama;
                    const harga = e.target.dataset.harga;

                    priceInfo.textContent = `Set harga untuk ${nama} (${kode})`;
                    priceKode.value = kode;
                    priceValue.value = harga ? parseInt(harga) : '';
                    modal.classList.remove('hidden');
                }
            });

            // Tutup modal
            priceCancel.addEventListener('click', () => modal.classList.add('hidden'));

            // Submit harga (AJAX)
            formSetPrice.addEventListener('submit', async (e) => {
                e.preventDefault();

                const payload = {
                    kode_barang: priceKode.value,
                    harga_jual: priceValue.value
                };

                const res = await fetch('{{ route('admin.products.set-price') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                let json = {};
                try { json = await res.json(); } catch (_) { }

                if (res.ok && json.status === 'success') {
                    alert('✅ ' + json.message);
                    modal.classList.add('hidden');
                    // cara cepat: refresh halaman agar kolom harga ter-update
                    location.reload();
                } else {
                    alert('❌ Gagal menyimpan harga' + (json.message ? ': ' + json.message : ''));
                }
            });
        });
    </script>
@endpush

@push('scripts')
    <script>
        (function () {
            const modal = document.getElementById('modalBarcode');
            const bcInfo = document.getElementById('bcInfo');
            const bcPreview = document.getElementById('bcPreview');
            const bcDownload = document.getElementById('bcDownload');
            const bcClose = document.getElementById('bcClose');

            // Open modal + fetch SVG
            document.addEventListener('click', async (e) => {
                if (!e.target.classList.contains('btn-barcode')) return;

                const previewUrl = e.target.dataset.preview;
                const downloadUrl = e.target.dataset.download;
                const kode = e.target.dataset.kode;
                const nama = e.target.dataset.nama;

                bcInfo.textContent = `${nama} (${kode})`;
                bcPreview.innerHTML = '<p class="text-gray-500">Memuat...</p>';

                try {
                    const res = await fetch(previewUrl);
                    if (!res.ok) throw new Error('Gagal memuat barcode');
                    const svg = await res.text();
                    bcPreview.innerHTML = svg;
                    bcDownload.setAttribute('href', downloadUrl);
                    modal.classList.remove('hidden');
                } catch (err) {
                    bcPreview.innerHTML = '<p class="text-red-600">Gagal memuat barcode.</p>';
                }
            });


            // Close modal
            bcClose.addEventListener('click', () => modal.classList.add('hidden'));
            modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
        })();
    </script>
@endpush