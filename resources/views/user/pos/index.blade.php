@extends('layouts.user')
@section('title','Transaksi | Kasirku')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* kecil2 anim */
@keyframes fadeUp { from {opacity:0; transform: translateY(6px);} to {opacity:1; transform:none;} }
.anim-in { animation: fadeUp .22s ease-out both; }
.badge { @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs; }
.card-btn { @apply rounded-md px-3 py-1.5 bg-indigo-600 text-white hover:opacity-90 transition; }
.qty-btn { @apply h-8 w-8 rounded-md border border-gray-300 hover:bg-gray-100; }
.sticky-total { position: sticky; bottom: 0; }
</style>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Kiri: Cari & Grid Produk --}}
  <section class="lg:col-span-2 card p-4 sm:p-6 anim-in">
    {{-- Search --}}
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
        <button id="refreshBtn" class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:opacity-90">Refresh</button>
      </div>
    </div>

    {{-- Grid Produk --}}
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
        <button class="mt-3 w-full card-btn addBtn">+ Tambah</button>
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

    {{-- List item --}}
    <div id="cartList" class="mt-4 space-y-3 overflow-y-auto" style="max-height: 48vh;">
      <p id="cartEmpty" class="text-gray-500">Belum ada item. Mulai cari atau scan barcode.</p>
    </div>

    {{-- Ringkasan & Bayar (sticky pada panel) --}}
    <div class="mt-auto pt-4 sticky-total bg-white">
      <div class="border-t pt-3 space-y-2 text-sm">
        <div class="flex justify-between"><span>Subtotal</span><span id="subtotalText">Rp 0</span></div>
        {{-- Diskon global (optional UI-ready, tidak diterapkan default) --}}
        <div class="flex items-center justify-between">
          <span>Diskon</span>
          <div class="flex items-center gap-1">
            <input id="discInput" type="number" min="0" value="0" class="w-24 rounded-md border-gray-300 text-right py-1 px-2">
            <span class="text-gray-500">Rp</span>
          </div>
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
      <div class="flex justify-between">
        <span>Subtotal</span><span id="paySubtotal">Rp 0</span>
      </div>
      <div class="flex justify-between">
        <span>Diskon</span><span id="payDisc">Rp 0</span>
      </div>
      <div class="flex justify-between text-lg font-semibold">
        <span>Total</span><span id="payTotal">Rp 0</span>
      </div>
    </div>

    <div class="mt-4">
      <label class="text-sm text-gray-600">Metode</label>
      <div class="mt-2 grid grid-cols-3 gap-2">
        <button class="methodBtn rounded-lg border py-2 hover:bg-gray-50" data-method="cash">Tunai</button>
        <button class="methodBtn rounded-lg border py-2 hover:bg-gray-50" data-method="noncash">Non-Tunai</button>
        <button class="methodBtn rounded-lg border py-2 hover:bg-gray-50" data-method="mixed">Campuran</button>
      </div>
    </div>

    <div id="cashWrap" class="mt-4 hidden">
      <label class="text-sm text-gray-600">Uang Diterima (Tunai)</label>
      <input id="cashInput" type="number" min="0" class="mt-1 w-full rounded-md border-gray-300 px-3 py-2">
      <p class="mt-2 text-sm">Kembalian: <span class="font-semibold" id="kembalianText">Rp 0</span></p>
    </div>

    <div class="mt-5 flex justify-end gap-2">
      <button id="cancelPay" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">Batal</button>
      <button id="finishPay" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:opacity-90">Selesaikan</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const rupiah = n => new Intl.NumberFormat('id-ID', {style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n||0);
const el = s => document.querySelector(s);
const els = s => Array.from(document.querySelectorAll(s));

let PRODUCTS = [];     // dari server (stok lokal kasir)
let CART = [];         // state keranjang

// ------ Fetch products ------
async function loadProducts(q='') {
  const url = new URL('{{ route('user.pos.products') }}', window.location.origin);
  if (q) url.searchParams.set('q', q);
  const res = await fetch(url);
  const json = await res.json();
  if (json.status === 'success') {
    PRODUCTS = json.data;
    renderGrid();
  } else {
    PRODUCTS = [];
    renderGrid();
  }
}

function renderGrid() {
  const wrap = el('#gridWrap');
  wrap.innerHTML = '';
  if (!PRODUCTS.length) {
    el('#emptyGrid').classList.remove('hidden');
    return;
  }
  el('#emptyGrid').classList.add('hidden');
  const tpl = el('#productCardTpl');

  PRODUCTS.forEach(p => {
    const node = tpl.content.cloneNode(true);
    node.querySelector('.name').textContent = p.nama_barang;
    node.querySelector('.price').textContent = p.harga_jual !== null ? rupiah(p.harga_jual) : '—';
    node.querySelector('.stok').textContent = `Stok ${p.stok_kasir}`;

    const btn = node.querySelector('.addBtn');
    if (p.stok_kasir <= 0) { btn.disabled = true; btn.classList.add('opacity-50'); btn.textContent='Habis'; }
    btn.addEventListener('click', () => addToCart(p));

    wrap.appendChild(node);
  });
}

// ------ Cart helpers ------
function addToCart(p) {
  if (p.harga_jual === null) {
    alert(`Harga belum diatur untuk ${p.nama_barang}. Minta admin set harga terlebih dahulu.`);
    return;
  }
  const idx = CART.findIndex(i => i.kode_barang === p.kode_barang);
  if (idx >= 0) {
    if (CART[idx].qty < p.stok_kasir) CART[idx].qty++;
  } else {
    CART.push({ kode_barang: p.kode_barang, nama: p.nama_barang, harga: p.harga_jual, qty: 1, stok: p.stok_kasir });
  }
  renderCart();
}

function removeFromCart(kode) {
  CART = CART.filter(i => i.kode_barang !== kode);
  renderCart();
}

function setQty(kode, qty) {
  const item = CART.find(i => i.kode_barang === kode);
  if (!item) return;
  qty = Math.max(0, Math.min(qty, item.stok));
  if (qty === 0) return removeFromCart(kode);
  item.qty = qty;
  renderCart();
}

function calc() {
  const subtotal = CART.reduce((s,i)=> s + (i.harga * i.qty), 0);
  const disc = parseInt(el('#discInput').value||0);
  const total = Math.max(0, subtotal - disc);
  return {subtotal, disc, total};
}

function renderCart() {
  const list = el('#cartList');
  list.innerHTML = '';
  if (!CART.length) {
    el('#cartEmpty').classList.remove('hidden');
  } else {
    el('#cartEmpty').classList.add('hidden');
    CART.forEach(i => {
      const row = document.createElement('div');
      row.className = 'border rounded-lg p-3 bg-white anim-in';
      row.innerHTML = `
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="font-medium">${i.nama}</p>
            <p class="text-sm text-gray-600">${rupiah(i.harga)} • Stok: ${i.stok}</p>
          </div>
          <button class="text-red-600 hover:underline" aria-label="hapus">Hapus</button>
        </div>
        <div class="mt-2 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <button class="qty-minus qty-btn">−</button>
            <input class="qty-input w-14 text-center rounded-md border border-gray-300 py-1" type="number" min="1" max="${i.stok}" value="${i.qty}">
            <button class="qty-plus qty-btn">+</button>
          </div>
          <div class="font-semibold">${rupiah(i.harga * i.qty)}</div>
        </div>
        ${i.qty > i.stok ? '<p class="mt-1 text-xs text-orange-600">Melebihi stok!</p>' : ''}
      `;
      row.querySelector('.qty-minus').addEventListener('click', ()=> setQty(i.kode_barang, i.qty - 1));
      row.querySelector('.qty-plus').addEventListener('click',  ()=> setQty(i.kode_barang, i.qty + 1));
      row.querySelector('.qty-input').addEventListener('change', (e)=> setQty(i.kode_barang, parseInt(e.target.value||1)));
      row.querySelector('[aria-label="hapus"]').addEventListener('click', ()=> removeFromCart(i.kode_barang));
      list.appendChild(row);
    });
  }
  const {subtotal, disc, total} = calc();
  el('#subtotalText').textContent = rupiah(subtotal);
  el('#totalText').textContent    = rupiah(total);
}

// ------ Payment modal ------
function openPay() {
  if (!CART.length) return alert('Keranjang kosong.');
  const {subtotal, disc, total} = calc();
  el('#paySubtotal').textContent = rupiah(subtotal);
  el('#payDisc').textContent     = rupiah(disc);
  el('#payTotal').textContent    = rupiah(total);
  el('#cashWrap').classList.add('hidden');
  el('#payModal').classList.remove('hidden');
  el('#cashInput').value = '';
  el('#kembalianText').textContent = rupiah(0);
}

function closePay() { el('#payModal').classList.add('hidden'); }

els('.methodBtn').forEach(b=>{
  b.addEventListener('click', ()=>{
    els('.methodBtn').forEach(x=>x.classList.remove('bg-indigo-50','border-indigo-600','text-indigo-700'));
    b.classList.add('bg-indigo-50','border-indigo-600','text-indigo-700');
    const m = b.dataset.method;
    el('#cashWrap').classList.toggle('hidden', m!=='cash' && m!=='mixed');
  });
});

el('#cashInput').addEventListener('input', ()=>{
  const paid = parseInt(el('#cashInput').value||0);
  const total = calc().total;
  el('#kembalianText').textContent = rupiah(Math.max(0, paid - total));
});

el('#finishPay').addEventListener('click', ()=>{
  // UI-only: belum kirim ke server
  alert('Transaksi selesai (UI demo). Nanti kita sambungkan ke endpoint checkout.');
  CART = [];
  renderCart();
  closePay();
});

// ------ Events ------
el('#refreshBtn').addEventListener('click', ()=> loadProducts(el('#searchInput').value.trim()));
el('#clearSearch').addEventListener('click', ()=> { el('#searchInput').value=''; loadProducts(''); el('#searchInput').focus(); });
el('#discInput').addEventListener('input', renderCart);
el('#payBtn').addEventListener('click', openPay);
el('#closePay').addEventListener('click', closePay);
el('#cancelPay').addEventListener('click', closePay);
el('#clearCart').addEventListener('click', ()=> { if (confirm('Kosongkan keranjang?')) { CART=[]; renderCart(); } });

// search debounce
let t=null;
el('#searchInput').addEventListener('input', e=>{
  clearTimeout(t);
  t=setTimeout(()=> loadProducts(e.target.value.trim()), 250);
});

// shortcuts
document.addEventListener('keydown', (e)=>{
  if (e.key==='F2'){ e.preventDefault(); el('#searchInput').focus(); }
  if ((e.ctrlKey || e.metaKey) && (e.key==='b' || e.key==='B')){ e.preventDefault(); openPay(); }
});

// init
loadProducts();
renderCart();
</script>
@endpush
