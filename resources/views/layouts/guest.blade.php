<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Kasirku')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased flex flex-col">
  {{-- Header --}}
  <header class="w-full px-4 sm:px-6 lg:px-8 py-4">
    @yield('brand')
  </header>

  {{-- Konten utama --}}
  <main class="flex-grow flex items-center justify-center">
    @yield('content')
  </main>
</body>
</html>
