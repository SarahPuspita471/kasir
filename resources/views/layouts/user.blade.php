<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Kasirku')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
</head>
<body class="layout-shell bg-gray-50 text-gray-900 antialiased">

  {{-- Topbar --}}
  <header class="header-blur sticky top-0 z-40">
    <div class="px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
      <x-ui.brand logo-src="/assets/logos/brand.svg" app-name="Kasirku" />
      <div class="flex items-center gap-3">
        <span class="hidden sm:block text-sm text-gray-600">Hi, {{ auth()->user()->name ?? 'User' }}</span>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <x-ui.button type="submit" variant="primary" class="w-auto h-9 px-3">Keluar</x-ui.button>
        </form>
      </div>
    </div>

    {{-- Tabs navigasi --}}
    <nav class="tabs px-4 sm:px-6 lg:px-8 pb-3">
      <div class="flex gap-2">
        <a href="{{ route('user.dashboard') }}"
           class="tab {{ request()->routeIs('user.dashboard') ? 'tab-active' : '' }}">🏠 Beranda</a>

        <a href="{{ route('user.pos.index') }}"
           class="tab {{ request()->routeIs('user.pos.*') ? 'tab-active' : '' }}">🧾 Transaksi</a>

        <a href="{{ route('user.history.index') }}"
           class="tab {{ request()->routeIs('user.history.*') ? 'tab-active' : '' }}">📜 Riwayat</a>

        <a href="{{ route('user.stock.index') }}"
           class="tab {{ request()->routeIs('user.stock.*') ? 'tab-active' : '' }}">📦 Stok</a>

        <a href="{{ route('user.customers.index') }}"
           class="tab {{ request()->routeIs('user.customers.*') ? 'tab-active' : '' }}">👥 Pelanggan</a>

        <a href="{{ route('user.reports.index') }}"
           class="tab {{ request()->routeIs('user.reports.*') ? 'tab-active' : '' }}">📊 Laporan</a>
      </div>
    </nav>
  </header>

  {{-- Konten melebar penuh --}}
  <main class="px-4 sm:px-6 lg:px-8 py-6">
    @yield('content')
  </main>

  @stack('scripts')
</body>
</html>
