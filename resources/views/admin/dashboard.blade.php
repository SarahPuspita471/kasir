@extends('layouts.admin')
@section('title','Dashboard Admin | Kasirku')

@section('content')
  {{-- Header/aksi cepat opsional --}}
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Dashboard Admin</h1>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:opacity-95 transition">
      + User Baru
    </a>
  </div>

  {{-- Hero cards: now full width --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 anim-card-in">
    <div class="card p-4"><p class="text-xs text-gray-500">Transaksi Hari Ini</p><p class="mt-1 text-2xl font-semibold">152</p></div>
    <div class="card p-4"><p class="text-xs text-gray-500">Pendapatan</p><p class="mt-1 text-2xl font-semibold">Rp 12.540.000</p></div>
    <div class="card p-4"><p class="text-xs text-gray-500">Item Terjual</p><p class="mt-1 text-2xl font-semibold">879</p></div>
    <div class="card p-4"><p class="text-xs text-gray-500">Pelanggan Baru</p><p class="mt-1 text-2xl font-semibold">23</p></div>
  </div>

  {{-- Seksi konten melebar --}}
  <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="card p-6 lg:col-span-2 anim-card-in anim-delay-1">
      <h2 class="text-lg font-semibold">Ringkasan Penjualan</h2>
      <p class="text-sm text-gray-600">Grafik/overview (akan kita isi kemudian).</p>
      <div class="mt-4 h-48 rounded-lg bg-gray-100"></div>
    </div>
    <div class="card p-6 anim-card-in anim-delay-2">
      <h2 class="text-lg font-semibold">Aktivitas Sistem</h2>
      <ul class="mt-3 space-y-2 text-sm text-gray-600">
        <li>• User baru ditambahkan</li>
        <li>• Harga produk diupdate</li>
        <li>• Laporan bulanan digenerasi</li>
      </ul>
    </div>
  </div>

  {{-- Aksi cepat --}}
  <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <a href="{{ route('admin.users.index') }}" class="card p-5 hover:shadow-lg transition anim-card-in">
      <p class="text-sm text-gray-600">Kelola</p>
      <p class="mt-1 text-lg font-semibold">Daftar User</p>
    </a>
    <a href="{{ route('admin.users.create') }}" class="card p-5 hover:shadow-lg transition anim-card-in anim-delay-1">
      <p class="text-sm text-gray-600">Tambah</p>
      <p class="mt-1 text-lg font-semibold">User Baru</p>
    </a>
    <a href="{{ route('admin.dashboard') }}" class="card p-5 hover:shadow-lg transition anim-card-in anim-delay-2">
      <p class="text-sm text-gray-600">Lihat</p>
      <p class="mt-1 text-lg font-semibold">Ringkasan Penjualan</p>
    </a>
    
  </div>
@endsection
