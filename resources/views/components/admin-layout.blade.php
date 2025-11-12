@props(['title' => 'Admin • HepatitisCase'])

<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
</head>
<body class="h-full">
  <div class="min-h-full">
    {{-- Navbar admin (gunakan komponenmu) --}}
    <nav class="bg-gray-800 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center">
        <x-admin-navbar />
      </div>
    </nav>

    {{-- Header: judul + slot aksi (mis. Logout) --}}
    <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <h1 class="text-xl font-semibold">{{ $title }}</h1>
      @isset($actions)
        <div>{{ $actions }}</div>
      @endisset
    </header>

    <main>
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-8">
        {{ $slot }}
      </div>
    </main>
  </div>

  @stack('scripts')
</body>
</html>
