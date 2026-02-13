<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Aplikasi POS (Point of Safe) modern dan ringan untuk mengelola stok, inventaris, penjualan, serta laporan keuangan bisnis Anda secara real-time.">
    <meta name="keywords" content="Aplikasi POS, Sistem Kasir, Manajemen Inventaris, Point of Sale, Laporan Keuangan Bisnis, Kasir Online, POS Indonesia">
    <meta name="author" content="{{ config('app.name', 'Laravel') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <script>
        function applyTheme() {
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark')
            }
        }
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        :root {
            --print-size: auto;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            @page {
                size: var(--print-size);
                margin: 0;
            }

            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="no-print">
            <livewire:layout.navigation />
        </div>
        @if(auth()->check() && auth()->user()->saldo <= env('LIMIT'))
            <div class="no-print bg-red-500 text-white text-center py-2 px-4 animate-pulse">
            <span class="font-bold">Peringatan:</span> Saldo limit, tidak bisa melakukan transaksi pembelian dan penjualan.
    </div>
    @endif

    <!-- Page Heading -->
    @if (isset($header))
    <header class="no-print bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
    @endif

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('alert', (event) => {
                alert(event.message || event[0].message);
            });
        });
    </script>
</body>

</html>