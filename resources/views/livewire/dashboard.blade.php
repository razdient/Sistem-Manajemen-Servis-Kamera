<div wire:keydown.escape.window="closeIntakeModal">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold">Ringkasan Dashboard</h2>
            <p class="mt-1 text-sm text-muted">
                Pantau aktivitas dan performa toko servis kamera.
            </p>
        </div>

        <button
            wire:click="openIntakeModal"
            class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:opacity-90"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Servis
        </button>
    </div>

    @if ($savedServiceNumber)
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-green-300 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/30">
            <div class="flex items-center gap-3">
                <span class="rounded-full bg-green-600 p-2 text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-green-800 dark:text-green-200">Servis berhasil dicatat</p>
                    <p class="text-xs text-green-700 dark:text-green-300">Nomor servis: {{ $savedServiceNumber }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('services.show', $savedVisitId) }}"
                    class="rounded-lg border border-green-300 bg-white px-3 py-2 text-xs font-medium text-green-800 hover:bg-green-100 dark:border-green-700 dark:bg-transparent dark:text-green-200 dark:hover:bg-green-900/40">
                    Lihat Detail
                </a>

                <a href="{{ route('services.receipt', $savedVisitId) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 17H4a1 1 0 01-1-1v-5a2 2 0 012-2h14a2 2 0 012 2v5a1 1 0 01-1 1h-2M6 14h12v7H6v-7z"/></svg>
                    Tanda Terima
                </a>
            </div>
        </div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-border bg-surface p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-muted">Total Kunjungan</p>
                <span class="rounded-lg bg-surface-secondary p-2 text-muted">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h12M9 12h12M9 19h12M4 5h.01M4 12h.01M4 19h.01"/></svg>
                </span>
            </div>
            <h3 class="mt-3 text-3xl font-bold">{{ $totalVisits }}</h3>
            <p class="mt-2 text-xs text-muted">Seluruh kunjungan servis</p>
        </div>

        <div class="rounded-xl border border-border bg-surface p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-muted">Servis Aktif</p>
                <span class="rounded-lg bg-surface-secondary p-2 text-muted">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <h3 class="mt-3 text-3xl font-bold">{{ $activeVisits }}</h3>
            <p class="mt-2 text-xs text-muted">Masih dalam proses</p>
        </div>

        <div class="rounded-xl border border-border bg-surface p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-muted">Servis Selesai</p>
                <span class="rounded-lg bg-surface-secondary p-2 text-muted">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <h3 class="mt-3 text-3xl font-bold">{{ $completedVisits }}</h3>
            <p class="mt-2 text-xs text-muted">Semua perangkat selesai</p>
        </div>

        <div class="rounded-xl border border-border bg-surface p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-muted">Siap Diambil</p>
                <span class="rounded-lg bg-surface-secondary p-2 text-muted">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </span>
            </div>
            <h3 class="mt-3 text-3xl font-bold">{{ $readyForPickup }}</h3>
            <p class="mt-2 text-xs text-muted">Perangkat siap diambil pelanggan</p>
        </div>
    </div>

    <div class="mb-6 grid gap-6 xl:grid-cols-3">
        <div class="rounded-xl border border-border bg-surface p-5 xl:col-span-2">
            <div class="mb-5">
                <h3 class="font-bold">Grafik Pendapatan</h3>
                <p class="text-sm text-muted">Pembayaran diterima 7 hari terakhir</p>
            </div>

            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Pendapatan Hari Ini</p>
            <h3 class="mt-3 text-3xl font-bold">{{ $formatRupiah($todayIncome) }}</h3>

            <div class="mt-5 space-y-4 border-t border-border pt-4 text-sm">
                <div>
                    <p class="text-muted">Pendapatan Bulan Ini</p>
                    <p class="mt-1 font-semibold">{{ $formatRupiah($monthlyIncome) }}</p>
                </div>
                <div>
                    <p class="text-muted">Tagihan Belum Lunas</p>
                    <p class="mt-1 font-semibold text-red-700 dark:text-red-400">{{ $formatRupiah($unpaidBalance) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-border bg-surface">
        <div class="border-b border-border p-5">
            <h3 class="font-bold">Servis Terbaru</h3>
            <p class="text-sm text-muted">Daftar kunjungan servis terakhir</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-secondary text-muted">
                    <tr>
                        <th class="px-5 py-3">No. Servis</th>
                        <th class="px-5 py-3">Pelanggan</th>
                        <th class="px-5 py-3">Perangkat</th>
                        <th class="px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentVisits as $visit)
                        <tr class="border-t border-border">
                            <td class="px-5 py-4 font-medium">
                                <a href="{{ route('services.show', $visit->id) }}" class="hover:underline">
                                    {{ $visit->service_number }}
                                </a>
                            </td>
                            <td class="px-5 py-4">{{ $visit->customer->name }}</td>
                            <td class="px-5 py-4">{{ $visit->devices->count() }} perangkat</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-surface-secondary px-3 py-1 text-xs font-medium">
                                    {{ $visit->status_labels }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-muted">
                                Belum ada data servis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($showIntakeModal)
        <div
            class="app-modal-backdrop fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-sm sm:p-6"
            wire:click.self="closeIntakeModal"
        >
            <div class="app-modal-panel my-4 w-full max-w-4xl overflow-hidden rounded-2xl border border-border bg-background shadow-2xl">
                <div class="flex items-center justify-between gap-4 border-b border-border bg-surface px-5 py-4">
                    <div>
                        <h3 class="font-bold">Penerimaan Servis Baru</h3>
                        <p class="text-xs text-muted">Isi data pelanggan dan perangkat yang diterima.</p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeIntakeModal"
                        class="rounded-lg p-2 text-muted hover:bg-surface-secondary hover:text-foreground"
                        aria-label="Tutup form"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveIntake" class="max-h-[calc(90vh-5rem)] overflow-y-auto p-5 sm:p-6">
                    @include('livewire.partials.service-intake-form')
                </form>
            </div>
        </div>
    @endif
</div>

@script
<script>
    const canvas = document.getElementById('revenueChart');

    function getChartColors() {
        const isDark = document.documentElement.classList.contains('dark');

        return {
            line: isDark ? '#f5f5f5' : '#171717',
            fill: isDark
                ? 'rgba(245, 245, 245, 0.08)'
                : 'rgba(23, 23, 23, 0.08)',
            text: isDark ? '#a3a3a3' : '#737373',
            grid: isDark ? '#333333' : '#e5e5e5',
        };
    }

    function updateChartTheme(chart) {
        const colors = getChartColors();

        chart.data.datasets[0].borderColor = colors.line;
        chart.data.datasets[0].backgroundColor = colors.fill;
        chart.options.scales.y.ticks.color = colors.text;
        chart.options.scales.y.grid.color = colors.grid;
        chart.options.scales.x.ticks.color = colors.text;
        chart.options.scales.x.grid.color = colors.grid;
        chart.update();
    }

    if (canvas && !canvas.chartInstance) {
        const colors = getChartColors();

        canvas.chartInstance = new Chart(canvas, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pemasukan',
                    data: @json($chartData),
                    borderColor: colors.line,
                    backgroundColor: colors.fill,
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: colors.text,
                            callback: value =>
                                'Rp' + value.toLocaleString('id-ID')
                        },
                        grid: { color: colors.grid }
                    },
                    x: {
                        ticks: { color: colors.text },
                        grid: { color: colors.grid }
                    }
                }
            }
        });

        window.addEventListener('theme-changed', () => {
            updateChartTheme(canvas.chartInstance);
        });
    }
</script>
@endscript
