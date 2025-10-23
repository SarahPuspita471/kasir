<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin | Kasirku')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="layout-shell bg-gray-50 text-gray-900 antialiased">

    {{-- Header full width --}}
    <header class="header-blur sticky top-0 z-40">
        <div class="px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="btnSidebar"
                    class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    <span class="sr-only">Toggle menu</span> ☰
                </button>
                <x-ui.brand logo-src="/assets/logos/brand.svg" app-name="Kasirku Admin" />
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden sm:block text-sm text-gray-600">Hi, {{ auth()->user()->name ?? 'Admin' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-ui.button type="submit" variant="primary" class="w-auto h-9 px-3">Keluar</x-ui.button>
                </form>
            </div>
        </div>
    </header>

    {{-- Full-width layout: sidebar + content --}}
    <div class="px-4 sm:px-6 lg:px-8 py-6 lg:grid lg:grid-cols-[260px_1fr] lg:gap-6">
        {{-- Sidebar: off-canvas di mobile, static di desktop --}}
        <aside id="sidebar" class="sidebar sidebar-hidden fixed inset-y-14 left-0 w-64 bg-white border-r border-gray-200 p-4 card
                  lg:static lg:w-auto lg:inset-auto lg:translate-x-0 lg:shadow-none lg:rounded-lg">
            <nav class="space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}"
                    class="block rounded-md px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 font-medium' : '' }}">
                    🏠 Dashboard
                </a>
                <div class="mt-3">
                    <p class="px-3 pb-1 text-xs uppercase tracking-wide text-gray-500">Manajemen</p>
                    <a href="{{ route('admin.users.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 font-medium' : '' }}">
                        👥 Kelola User
                    </a>
                    <a href="{{ route('admin.stock.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.stock.*') ? 'bg-gray-100 font-medium' : '' }}">
                        📦 Stok
                    </a>
                    <a href="{{ route('admin.vouchers.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.vouchers.*') ? 'bg-gray-100 font-medium' : '' }}">
                        🎟️ Voucher
                    </a>
                    <a href="{{ route('admin.discounts.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.discounts.*') ? 'bg-gray-100 font-medium' : '' }}">
                        ⚖️ Diskon Otomatis
                    </a>
                    <a href="{{ route('admin.tax.edit') }}"
                        class="block rounded-md px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.tax.*') ? 'bg-gray-100 font-medium' : '' }}">
                        💰 Tax
                    </a>

                    <a href="{{ route('admin.transactions.index') }}"
                     class="block rounded-md px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.transactions.*') ? 'bg-gray-100 font-medium' : '' }}">🧾 Transaksi
                    </a>
                    <a href="#" class="block rounded-md px-3 py-2 hover:bg-gray-100">📊 Laporan</a>
                </div>
            </nav>
        </aside>

        {{-- Content: isi melebar penuh --}}
        <main class="flex-1 min-w-0">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        // Toggle sidebar di mobile
        (function () {
            const btn = document.getElementById('btnSidebar');
            const sb = document.getElementById('sidebar');
            btn?.addEventListener('click', () => {
                sb?.classList.toggle('sidebar-hidden');
            });
        })();
    </script>
</body>

</html>