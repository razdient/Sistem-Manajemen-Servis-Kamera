<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CameraService' }}</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/jpeg">
    <script>
        (function () {
            const theme = localStorage.getItem('camera-service-theme') ?? 'dark';
            const dark = theme === 'dark';
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-aurora" aria-hidden="true">
        <span class="app-aurora-blob app-aurora-blob-1"></span>
        <span class="app-aurora-blob app-aurora-blob-2"></span>
        <span class="app-aurora-blob app-aurora-blob-3"></span>
    </div>

    <div class="relative z-10 min-h-screen text-foreground md:flex">

        <!-- Sidebar -->
        <aside class="w-full border-b border-border bg-surface/85 p-5 backdrop-blur-xl md:min-h-screen md:w-64 md:border-b-0 md:border-r">
            <div class="mb-8 flex items-center gap-3">
                <img src="{{ asset('images/logo.jpg') }}" alt="Dinamika Kamera" class="h-11 w-11 rounded-lg object-cover">
                <div>
                    <h1 class="text-lg font-bold leading-tight tracking-tight">Dinamika Kamera</h1>
                    <p class="text-xs text-muted">Purwokerto</p>
                </div>
            </div>

            <nav class="space-y-1">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-muted">Menu Utama</p>

                <a href="{{ route('dashboard') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors',
                       'bg-primary text-primary-foreground' => request()->routeIs('dashboard'),
                       'text-muted hover:bg-surface-secondary hover:text-foreground' => !request()->routeIs('dashboard'),
                   ])>
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9.5z"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('customers.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors',
                       'bg-primary text-primary-foreground' => request()->routeIs('customers.index'),
                       'text-muted hover:bg-surface-secondary hover:text-foreground' => !request()->routeIs('customers.index'),
                   ])>
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 00-8 0m8 0h4m-4 0H7m-4 0h4m1-11a4 4 0 118 0 4 4 0 01-8 0zm9 3a3 3 0 100-6 3 3 0 000 6zm0 8v-1a5 5 0 00-2.5-4.33"/></svg>
                    <span>Data Pelanggan</span>
                </a>

                <a href="{{ route('services.create') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors',
                       'bg-primary text-primary-foreground' => request()->routeIs('services.create'),
                       'text-muted hover:bg-surface-secondary hover:text-foreground' => !request()->routeIs('services.create'),
                   ])>
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Penerimaan Servis</span>
                </a>

                <a href="{{ route('services.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors',
                       'bg-primary text-primary-foreground' => request()->routeIs('services.index'),
                       'text-muted hover:bg-surface-secondary hover:text-foreground' => !request()->routeIs('services.index'),
                   ])>
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h12M9 12h12M9 19h12M4 5h.01M4 12h.01M4 19h.01"/></svg>
                    <span>Daftar Servis</span>
                </a>

                <a href="{{ route('catalogs.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors',
                       'bg-primary text-primary-foreground' => request()->routeIs('catalogs.index'),
                       'text-muted hover:bg-surface-secondary hover:text-foreground' => !request()->routeIs('catalogs.index'),
                   ])>
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M3 5a2 2 0 012-2h4l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/></svg>
                    <span>Katalog Harga</span>
                </a>

                <a href="{{ route('payments.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors',
                       'bg-primary text-primary-foreground' => request()->routeIs('payments.*') || request()->routeIs('services.payment'),
                       'text-muted hover:bg-surface-secondary hover:text-foreground' => ! (request()->routeIs('payments.*') || request()->routeIs('services.payment')),
                   ])>
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 11h18M5 17h4m-6 2h18a2 2 0 002-2V7a2 2 0 00-2-2H3a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Pembayaran</span>
                </a>

                <p class="mb-2 mt-7 text-xs font-semibold uppercase tracking-wider text-muted">Lainnya</p>

                <a href="{{ route('expenses.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors',
                       'bg-primary text-primary-foreground' => request()->routeIs('expenses.*'),
                       'text-muted hover:bg-surface-secondary hover:text-foreground' => !request()->routeIs('expenses.*'),
                   ])>
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pengeluaran</span>
                </a>

                <a href="{{ route('reports.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors',
                       'bg-primary text-primary-foreground' => request()->routeIs('reports.*'),
                       'text-muted hover:bg-surface-secondary hover:text-foreground' => !request()->routeIs('reports.*'),
                   ])>
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 16V9m5 7V5m5 11v-7"/></svg>
                    <span>Laporan</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-muted transition-colors hover:bg-surface-secondary hover:text-foreground">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317a1 1 0 011.35-.436l.35.175a1 1 0 00.95 0l.35-.175a1 1 0 011.35.436l.17.352a1 1 0 00.672.546l.383.096a1 1 0 01.758.97v.392a1 1 0 00.276.69l.27.27a1 1 0 010 1.414l-.27.27a1 1 0 00-.276.69v.392a1 1 0 01-.758.97l-.383.096a1 1 0 00-.672.546l-.17.352a1 1 0 01-1.35.436l-.35-.175a1 1 0 00-.95 0l-.35.175a1 1 0 01-1.35-.436l-.17-.352a1 1 0 00-.672-.546l-.383-.096a1 1 0 01-.758-.97v-.392a1 1 0 00-.276-.69l-.27-.27a1 1 0 010-1.414l.27-.27a1 1 0 00.276-.69v-.392a1 1 0 01.758-.97l.383-.096a1 1 0 00.672-.546l.17-.352zM12 15a3 3 0 100-6 3 3 0 000 6z"/></svg>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <main class="min-w-0 flex-1">
            <header class="flex items-center justify-between border-b border-border bg-surface/85 px-6 py-4 backdrop-blur-xl">
                <div>
                    <h2 class="font-semibold">{{ $title ?? 'CameraService' }}</h2>
                    <p class="text-xs text-muted">Ringkasan aktivitas toko servis kamera</p>
                </div>

                <button
                    onclick="toggleTheme()"
                    class="flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm hover:bg-surface-secondary"
                    aria-label="Ganti tema"
                >
                    <svg class="h-4 w-4 dark:hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3c.2 0 .4 0 .6.02A7 7 0 0021 12.79z"/></svg>
                    <svg class="hidden h-4 w-4 dark:block" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414m0-11.314L7.05 7.05m9.9 9.9l1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                    <span class="dark:hidden">Mode Gelap</span>
                    <span class="hidden dark:inline">Mode Terang</span>
                </button>
            </header>

            <section class="p-6">
                {{ $slot }}
            </section>
        </main>

    </div>
</body>
</html>
