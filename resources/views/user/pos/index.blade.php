@extends('layouts.user')
@section('title', 'Transaksi | Kasirku')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(6px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .anim-in {
            animation: fadeUp .22s ease-out both;
        }

        .badge {
            @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs;
        }

        .card-btn {
            @apply rounded-md px-3 py-1.5 bg-indigo-600 text-white hover:opacity-90 transition;
        }

        .qty-btn {
            @apply h-8 w-8 rounded-md border border-gray-300 hover:bg-gray-100;
        }

        .sticky-total {
            position: sticky;
            bottom: 0;
        }
    </style>
@endpush

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kiri: Cari & Grid Produk --}}
        <section class="lg:col-span-2 card p-4 sm:p-6 anim-in">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label class="text-sm text-gray-600">Cari / Scan barcode</label>
                    <div class="mt-1 relative">
                        <input id="searchInput" type="text" placeholder="Ketik nama, kode, atau scan..."
                            class="w-full rounded-md border border-gray-300 px-3 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="absolute left-3 top-2.5 text-gray-400">🔎</span>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <button id="clearSearch" class="px-3 py-2 rounded-md bg-gray-100 hover:bg-gray-200">Bersihkan</button>
                    <button id="refreshBtn"
                        class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:opacity-90">Refresh</button>
                </div>
            </div>

            <div id="gridWrap" class="mt-4 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                {{-- diisi via JS --}}
            </div>

            <template id="productCardTpl">
                <div class="border rounded-xl p-3 hover:shadow-sm transition bg-white anim-in">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-medium name line-clamp-2"></p>
                        <span class="badge bg-gray-100 text-gray-600 stok"></span>
                    </div>
                    <p class="mt-1 text-indigo-700 font-semibold price"></p>
                    <button type="button" class="mt-3 w-full card-btn addBtn" data-add="1" data-kode="">+ Tambah</button>
                </div>
            </template>

            <div id="emptyGrid" class="hidden text-center py-10 text-gray-500">Tidak ada produk yang cocok.</div>
        </section>

        {{-- Kanan: Keranjang --}}
        <aside class="card p-4 sm:p-6 flex flex-col h-full anim-in">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">🧺 Keranjang</h2>
                <button id="clearCart" class="text-sm text-red-600 hover:underline">Kosongkan</button>
            </div>

            <div class="mt-3">
                <label class="text-sm text-gray-600">Nama Pembeli (opsional)</label>
                <input id="customerName" type="text" class="mt-1 w-full rounded-md border-gray-300 px-3 py-2"
                    placeholder="cth. Budi">
            </div>

            <div id="cartList" class="mt-4 space-y-3 overflow-y-auto" style="max-height:48vh;">
                <p id="cartEmpty" class="text-gray-500">Belum ada item. Mulai cari atau scan barcode.</p>
            </div>

            <div class="mt-auto pt-4 sticky-total bg-white">
                <div class="border-t pt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><span>Subtotal</span><span id="subtotalText">Rp 0</span></div>

                    <div class="flex justify-between">
                        <span>Diskon Otomatis</span><span id="autoDiscText">Rp 0</span>
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <input id="voucherCode" type="text" placeholder="Kode voucher"
                                class="flex-1 rounded-md border-gray-300 py-1 px-2 uppercase">
                            <button id="applyVoucher"
                                class="px-3 py-1.5 rounded-md bg-indigo-600 text-white text-xs">Terapkan</button>
                            <button id="removeVoucher" class="px-3 py-1.5 rounded-md bg-gray-200 text-xs">Batal</button>
                        </div>
                        <p id="voucherInfo" class="mt-1 text-xs text-gray-500 hidden"></p>
                        <p id="voucherError" class="mt-1 text-xs text-red-600 hidden"></p>
                    </div>

                    <div class="flex justify-between">
                        <span>Potongan Voucher</span><span id="voucherDiscText">Rp 0</span>
                    </div>

                    {{-- Pajak (sidebar) --}}
                    <div class="flex justify-between text-sm">
                        <span id="tax-name">Pajak</span>
                        <span id="tax-amount">Rp 0</span>
                    </div>

                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total</span><span id="totalText">Rp 0</span>
                    </div>
                </div>

                <button id="payBtn" class="mt-3 w-full card-btn h-11 text-base">Bayar (Ctrl+B)</button>
            </div>
        </aside>
    </div>

    {{-- Modal Pembayaran --}}
    <div id="payModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-end md:items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 anim-in">
            <div class="flex items-start justify-between">
                <h3 class="text-xl font-semibold">💳 Pembayaran</h3>
                <button id="closePay" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <div class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span id="paySubtotal">Rp 0</span></div>
                <div class="flex justify-between"><span>Diskon</span><span id="payDisc">Rp 0</span></div>
                {{-- Pajak (modal) -> ID dibuat unik --}}
                <div class="flex justify-between text-sm">
                    <span id="payTaxName">Pajak</span>
                    <span id="payTaxAmount">Rp 0</span>
                </div>
                <div class="flex justify-between text-lg font-semibold"><span>Total</span><span id="payTotal">Rp 0</span>
                </div>
            </div>

            <div class="mt-4">
                <label class="text-sm text-gray-600">Metode</label>
                <div class="mt-2 grid grid-cols-3 gap-2">
                    <button type="button" class="methodBtn rounded-lg border py-2 hover:bg-gray-50"
                        data-method="cash">Tunai</button>
                    <button type="button" class="methodBtn rounded-lg border py-2 hover:bg-gray-50"
                        data-method="noncash">Non-Tunai</button>
                    <button type="button" class="methodBtn rounded-lg border py-2 hover:bg-gray-50"
                        data-method="mixed">Campuran</button>
                </div>
            </div>

            <div id="cashWrap" class="mt-4 hidden">
                <label class="text-sm text-gray-600">Uang Diterima (Tunai)</label>
                <input id="cashInput" type="number" min="0" class="mt-1 w-full rounded-md border-gray-300 px-3 py-2">
                <p class="mt-2 text-sm">Kembalian: <span class="font-semibold" id="kembalianText">Rp 0</span></p>
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <button id="cancelPay" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">Batal</button>
                <button id="finishPay"
                    class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:opacity-90">Selesaikan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const rupiah = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n || 0);
        const el = s => document.querySelector(s);
        const els = s => Array.from(document.querySelectorAll(s));

        let PRODUCTS = [];
        let PRODUCT_MAP = {};
        let CART = [];

        // DISCOUNT STATE
        let AUTO_DISC = 0;
        let VOUCHER = { amount: 0, redemption_id: null, code: null };

        // TAX STATE
        let TAX = { enabled: false, rate: 0, name: 'Pajak' };

        // ===== Fetch Products =====
        async function loadProducts(q = '') {
            const url = new URL('{{ route('user.pos.products') }}', window.location.origin);
            if (q) url.searchParams.set('q', q);
            try {
                const res = await fetch(url);
                const json = await res.json();
                PRODUCTS = (json.status === 'success') ? (json.data || []) : [];
            } catch { PRODUCTS = []; }
            renderGrid();
        }

        function renderGrid() {
            const wrap = el('#gridWrap'); if (!wrap) return;
            wrap.innerHTML = '';
            PRODUCT_MAP = {};

            if (!PRODUCTS.length) { const eg = el('#emptyGrid'); if (eg) eg.classList.remove('hidden'); return; }
            const eg = el('#emptyGrid'); if (eg) eg.classList.add('hidden');

            const tpl = el('#productCardTpl');
            PRODUCTS.forEach(p => {
                PRODUCT_MAP[p.kode_barang] = p;
                const node = tpl.content.cloneNode(true);
                node.querySelector('.name').textContent = p.nama_barang;
                node.querySelector('.price').textContent = (p.harga_jual != null) ? rupiah(p.harga_jual) : '—';
                node.querySelector('.stok').textContent = `Stok ${p.stok_kasir}`;
                const btn = node.querySelector('.addBtn');
                btn.dataset.kode = p.kode_barang;
                if (p.stok_kasir <= 0) { btn.disabled = true; btn.classList.add('opacity-50'); btn.textContent = 'Habis'; }
                wrap.appendChild(node);
            });
        }

        // Delegation for +Tambah
        el('#gridWrap').addEventListener('click', (e) => {
            const btn = e.target.closest('[data-add]');
            if (!btn) return;
            const p = PRODUCT_MAP[btn.dataset.kode];
            if (p) addToCart(p);
        });

        // ===== Cart Helpers =====
        function addToCart(p) {
            if (p.harga_jual == null) { alert(`Harga belum diatur untuk ${p.nama_barang}. Minta admin set harga terlebih dahulu.`); return; }
            const idx = CART.findIndex(i => i.kode_barang === p.kode_barang);
            if (idx >= 0) { if (CART[idx].qty < p.stok_kasir) CART[idx].qty++; }
            else { CART.push({ kode_barang: p.kode_barang, nama: p.nama_barang, harga: p.harga_jual, qty: 1, stok: p.stok_kasir }); }
            renderCart();
        }
        function removeFromCart(kode) { CART = CART.filter(i => i.kode_barang !== kode); renderCart(); }
        function setQty(kode, qty) {
            const it = CART.find(i => i.kode_barang === kode); if (!it) return;
            qty = Math.max(1, Math.min(qty, it.stok));
            it.qty = qty; renderCart();
        }

        // ===== Totals (dengan PAJAK) =====
        function refreshTotals() {
            const subtotal = CART.reduce((s, i) => s + (i.harga * i.qty), 0);
            const autoDisc = AUTO_DISC || 0;
            const voucher = VOUCHER.amount || 0;

            const dpp = Math.max(0, subtotal - autoDisc - voucher);
            const tax = TAX.enabled ? Math.floor(dpp * (TAX.rate / 100)) : 0;
            const grand = dpp + tax;

            // Sidebar
            const subEl = el('#subtotalText'); if (subEl) subEl.textContent = rupiah(subtotal);
            const adEl = el('#autoDiscText'); if (adEl) adEl.textContent = rupiah(autoDisc);
            const vdEl = el('#voucherDiscText'); if (vdEl) vdEl.textContent = rupiah(voucher);
            const taxNm = el('#tax-name'); if (taxNm) taxNm.textContent = TAX.enabled ? `${TAX.name} (${TAX.rate}%)` : TAX.name;
            const taxEl = el('#tax-amount'); if (taxEl) taxEl.textContent = rupiah(tax);
            const totEl = el('#totalText'); if (totEl) totEl.textContent = rupiah(grand);

            // Modal
            const paySub = el('#paySubtotal'); if (paySub) paySub.textContent = rupiah(subtotal);
            const payDis = el('#payDisc'); if (payDis) payDis.textContent = rupiah(autoDisc + voucher);
            const payTaxNm = el('#payTaxName'); if (payTaxNm) payTaxNm.textContent = TAX.enabled ? `${TAX.name} (${TAX.rate}%)` : TAX.name;
            const payTaxEl = el('#payTaxAmount'); if (payTaxEl) payTaxEl.textContent = rupiah(tax);
            const payTot = el('#payTotal'); if (payTot) payTot.textContent = rupiah(grand);

            // Simpan untuk dipakai di kembalian & checkout
            window.__POS_TOTALS__ = { subtotal, autoDisc, voucher, dpp, tax, grand };
        }

        async function previewAutoDiscount() {
            const subtotal = CART.reduce((s, i) => s + (i.harga * i.qty), 0);
            if (subtotal <= 0) { AUTO_DISC = 0; refreshTotals(); return; }
            try {
                const res = await fetch('{{ route('user.pos.discount.preview') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ subtotal })
                });
                const json = await res.json();
                AUTO_DISC = json?.data?.auto_discount?.amount || 0;
            } catch { AUTO_DISC = 0; }
            refreshTotals();
        }

        async function applyVoucher() {
            const code = (el('#voucherCode')?.value || '').trim();
            const err = el('#voucherError'); const info = el('#voucherInfo');
            if (err) err.classList.add('hidden'); if (info) info.classList.add('hidden');

            const subtotal = CART.reduce((s, i) => s + (i.harga * i.qty), 0);
            if (!code) { if (err) { err.textContent = 'Masukkan kode.'; err.classList.remove('hidden'); } return; }
            if (subtotal <= 0) { if (err) { err.textContent = 'Keranjang kosong.'; err.classList.remove('hidden'); } return; }

            try {
                const res = await fetch('{{ route('user.pos.voucher.validate') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ code, subtotal })
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json?.message || 'Voucher tidak valid');

                VOUCHER = { amount: json.data.amount, redemption_id: json.data.redemption_id, code: json.data.voucher.code };
                if (info) { info.textContent = `Voucher ${VOUCHER.code} diterapkan. Potongan ${rupiah(VOUCHER.amount)}.`; info.classList.remove('hidden'); }
            } catch (e) {
                VOUCHER = { amount: 0, redemption_id: null, code: null };
                if (err) { err.textContent = e.message; err.classList.remove('hidden'); }
            }
            refreshTotals();
        }

        async function removeVoucher() {
            const err = el('#voucherError'); const info = el('#voucherInfo');
            if (err) err.classList.add('hidden'); if (info) info.classList.add('hidden');

            if (VOUCHER.redemption_id) {
                try {
                    await fetch('{{ route('user.pos.voucher.void') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ redemption_id: VOUCHER.redemption_id })
                    });
                } catch { }
            }
            VOUCHER = { amount: 0, redemption_id: null, code: null };
            const vc = el('#voucherCode'); if (vc) vc.value = '';
            refreshTotals();
        }

        // ===== Render Cart =====
        function renderCart() {
            const list = el('#cartList'); if (!list) return;
            list.innerHTML = '';

            const emptyEl = el('#cartEmpty');
            if (!CART.length) { if (emptyEl) emptyEl.classList.remove('hidden'); refreshTotals(); return; }
            if (emptyEl) emptyEl.classList.add('hidden');

            CART.forEach(i => {
                const row = document.createElement('div');
                row.className = 'border rounded-lg p-3 bg-white anim-in';
                row.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <p class="font-medium">${i.nama}</p>
                    <p class="text-sm text-gray-600">${rupiah(i.harga)} • Stok: ${i.stok}</p>
                  </div>
                  <button type="button" class="text-red-600 hover:underline" aria-label="hapus">Hapus</button>
                </div>
                <div class="mt-2 flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <button type="button" class="qty-minus qty-btn">−</button>
                    <input class="qty-input w-14 text-center rounded-md border border-gray-300 py-1"
                           type="number" min="1" max="${i.stok}" value="${i.qty}">
                    <button type="button" class="qty-plus qty-btn">+</button>
                  </div>
                  <div class="font-semibold">${rupiah(i.harga * i.qty)}</div>
                </div>
                ${i.qty > i.stok ? '<p class="mt-1 text-xs text-orange-600">Melebihi stok!</p>' : ''}
            `;
                row.querySelector('.qty-minus').addEventListener('click', () => setQty(i.kode_barang, i.qty - 1));
                row.querySelector('.qty-plus').addEventListener('click', () => setQty(i.kode_barang, i.qty + 1));
                row.querySelector('.qty-input').addEventListener('change', e => setQty(i.kode_barang, parseInt(e.target.value || 1)));
                row.querySelector('[aria-label="hapus"]').addEventListener('click', () => removeFromCart(i.kode_barang));
                list.appendChild(row);
            });

            // hitung auto discount dari server, lalu refresh totals (yang sudah include pajak)
            previewAutoDiscount();
        }

        // ===== Payment modal =====
        function openPay() {
            if (!CART.length) { alert('Keranjang kosong.'); return; }
            refreshTotals();
            const cw = el('#cashWrap'); if (cw) cw.classList.add('hidden');
            const pm = el('#payModal'); if (pm) pm.classList.remove('hidden');
            const ci = el('#cashInput'); if (ci) ci.value = '';
            const kt = el('#kembalianText'); if (kt) kt.textContent = rupiah(0);
        }
        function closePay() { const pm = el('#payModal'); if (pm) pm.classList.add('hidden'); }

        els('.methodBtn').forEach(b => {
            b.addEventListener('click', () => {
                els('.methodBtn').forEach(x => x.classList.remove('bg-indigo-50', 'border-indigo-600', 'text-indigo-700'));
                b.classList.add('bg-indigo-50', 'border-indigo-600', 'text-indigo-700');
                const showCash = (b.dataset.method === 'cash' || b.dataset.method === 'mixed');
                const cw = el('#cashWrap'); if (cw) cw.classList.toggle('hidden', !showCash);
            });
        });

        const cashInput = el('#cashInput');
        if (cashInput) {
            cashInput.addEventListener('input', () => {
                const paid = parseInt(cashInput.value || 0);
                const totals = window.__POS_TOTALS__ || { grand: 0 };
                const kt = el('#kembalianText'); if (kt) kt.textContent = rupiah(Math.max(0, paid - (totals.grand || 0)));
            });
        }

        el('#finishPay').addEventListener('click', async () => {
            if (!CART.length) return alert('Keranjang kosong.');

            const items = CART.map(i => ({ kode_barang: i.kode_barang, qty: i.qty }));
            const totals = window.__POS_TOTALS__ || { grand: 0 };
            const total = totals.grand; // grand total = DPP + tax

            const showCash = !el('#cashWrap')?.classList.contains('hidden');
            let cashPaid = showCash ? parseInt(el('#cashInput')?.value || 0) : total;
            if (cashPaid < total) { alert('Uang yang diterima kurang dari total.'); return; }

            const payload = {
                customer_name: (el('#customerName')?.value || '').trim() || null,
                items, cash_paid: cashPaid,
                voucher: VOUCHER.redemption_id ? { redemption_id: VOUCHER.redemption_id, code: VOUCHER.code } : null
            };

            try {
                const res = await fetch('{{ route('user.pos.checkout') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok || json.status !== 'success') throw new Error(json.message || 'Checkout gagal');

                alert(`Transaksi sukses.\nKembalian: ${rupiah(json.data.change_due)}`);
                window.open(json.data.receipt_url, '_blank');

                CART = []; VOUCHER = { amount: 0, redemption_id: null, code: null }; AUTO_DISC = 0;
                const cn = el('#customerName'); if (cn) cn.value = '';
                const ci = el('#cashInput'); if (ci) ci.value = '';
                const kt = el('#kembalianText'); if (kt) kt.textContent = rupiah(0);
                renderCart(); closePay();
            } catch (e) { alert(e.message); }
        });

        // ===== UI Events =====
        el('#refreshBtn').addEventListener('click', () => loadProducts(el('#searchInput').value.trim()));
        el('#clearSearch').addEventListener('click', () => {
            const si = el('#searchInput'); if (si) { si.value = ''; si.focus(); }
            loadProducts('');
        });
        el('#payBtn').addEventListener('click', openPay);
        el('#closePay').addEventListener('click', closePay);
        el('#cancelPay').addEventListener('click', closePay);
        el('#clearCart').addEventListener('click', () => { if (confirm('Kosongkan keranjang?')) { CART = []; renderCart(); } });

        el('#applyVoucher').addEventListener('click', applyVoucher);
        el('#removeVoucher').addEventListener('click', removeVoucher);

        // Search debounce
        let t = null;
        el('#searchInput').addEventListener('input', e => {
            clearTimeout(t);
            t = setTimeout(() => loadProducts(e.target.value.trim()), 250);
        });

        // Shortcuts
        document.addEventListener('keydown', e => {
            if (e.key === 'F2') { e.preventDefault(); el('#searchInput')?.focus(); }
            if ((e.ctrlKey || e.metaKey) && (e.key === 'b' || e.key === 'B')) { e.preventDefault(); openPay(); }
        });

        // ===== init =====
        loadProducts();
        renderCart();

        // Ambil konfigurasi pajak
        fetch('{{ route('user.pos.tax.show') }}', { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(r => r.json())
            .then(d => {
                TAX.enabled = !!d.enabled;
                TAX.rate = Number(d.rate || 0);
                TAX.name = d.name || 'Pajak';
                // label sidebar & modal
                const nm1 = el('#tax-name'); if (nm1) nm1.textContent = TAX.enabled ? `${TAX.name} (${TAX.rate}%)` : TAX.name;
                const nm2 = el('#payTaxName'); if (nm2) nm2.textContent = TAX.enabled ? `${TAX.name} (${TAX.rate}%)` : TAX.name;
                refreshTotals();
            });
    </script>
@endpush