@extends('layouts.user')
@section('title','Dashboard | Kasirku')

@section('content')
  <div class="card p-6 anim-card-in">
    <h1 class="text-2xl font-semibold">Halo, {{ auth()->user()->name }} 👋</h1>
    <p class="mt-2 text-gray-600">Selamat bekerja! Pilih tab di atas untuk mulai transaksi.</p>
  </div>

  <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <a href="{{ route('user.pos.index') }}" class="card p-5 hover:shadow-lg transition anim-card-in">
      <p class="text-sm text-gray-600">Mulai</p>
      <p class="mt-1 text-lg font-semibold">Transaksi Baru</p>
    </a>
    <a href="{{ route('user.history.index') }}" class="card p-5 hover:shadow-lg transition anim-card-in anim-delay-1">
      <p class="text-sm text-gray-600">Lihat</p>
      <p class="mt-1 text-lg font-semibold">Riwayat Transaksi</p>
    </a>
    <a href="{{ route('user.stock.index') }}" class="card p-5 hover:shadow-lg transition anim-card-in anim-delay-2">
      <p class="text-sm text-gray-600">Cek</p>
      <p class="mt-1 text-lg font-semibold">Stok Produk</p>
    </a>
  </div>
@endsection
