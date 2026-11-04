<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CameraService' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen bg-background text-foreground md:flex">

        <!-- Sidebar -->
        <aside class="w-full border-b border-border bg-surface p-5 md:min-h-screen md:w-64 md:border-b-0 md:border-r">
            <div class="mb-8">
                <h1 class="text-xl font-bold tracking-tight">CameraService</h1>
                <p class="mt-1 text-xs text-muted">Sistem Manajemen Servis</p>
            </div>

            <nav class="space-y-1">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-muted">Menu Utama</p>

                <a href="/" class="block rounded-lg bg-primary px-4 py-3 text-sm font-medium text-primary-foreground">
                    Dashboard
                </a>

                <a href="#" class="block rounded-lg px-4 py-3 text-sm text-muted hover:bg-surface-secondary hover:text-foreground">
                    Data Pelanggan
                </a>

                <a href="#" class="block rounded-lg px-4 py-3 text-sm text-muted hover:bg-surface-secondary hover:text-foreground">
                    Data Servis
                </a>

                <a href="#" class="block rounded-lg px-4 py-3 text-sm text-muted hover:bg-surface-secondary hover:text-foreground">
                    Pembayaran
                </a>

                <p class="mb-2 mt-7 text-xs font-semibold uppercase tracking-wider text-muted">Lainnya</p>

                <a href="#" class="block rounded-lg px-4 py-3 text-sm text-muted hover:bg-surface-secondary hover:text-foreground">
                    Pengeluaran
                </a>

                <a href="#" class="block rounded-lg px-4 py-3 text-sm text-muted hover:bg-surface-secondary hover:text-foreground">
                    Laporan
                </a>

                <a href="#" class="block rounded-lg px-4 py-3 text-sm text-muted hover:bg-surface-secondary hover:text-foreground">
                    Pengaturan
                </a>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <main class="min-w-0 flex-1">
            <header class="flex items-center justify-between border-b border-border bg-surface px-6 py-4">
                <div>
                    <h2 class="font-semibold">Dashboard</h2>
                    <p class="text-xs text-muted">Ringkasan aktivitas toko servis kamera</p>
                </div>

                <button
                    onclick="toggleTheme()"
                    class="rounded-lg border border-border px-4 py-2 text-sm hover:bg-surface-secondary"
                >
                    Ganti Tema
                </button>
            </header>

            <section class="p-6">
                {{ $slot }}
            </section>
        </main>

    </div>
</body>
</html>
