
<div>
    <!-- Judul halaman -->
    <div class="mb-6">
        <h2 class="text-xl font-bold">Ringkasan Dashboard</h2>
        <p class="mt-1 text-sm text-muted">
            Pantau aktivitas dan performa toko servis kamera.
        </p>
    </div>

    <!-- Statistik -->
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Total Servis', '24', 'Seluruh kunjungan'],
            ['Sedang Dikerjakan', '8', 'Dalam proses servis'],
            ['Menunggu Persetujuan', '5', 'Menunggu keputusan pelanggan'],
            ['Siap Diambil', '3', 'Servis telah selesai']
        ] as $stat)
            <div class="rounded-xl border border-border bg-surface p-5">
                <p class="text-sm text-muted">{{ $stat[0] }}</p>
                <h3 class="mt-3 text-3xl font-bold">{{ $stat[1] }}</h3>
                <p class="mt-2 text-xs text-muted">{{ $stat[2] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Grafik dan pemasukan -->
    <div class="mb-6 grid gap-6 xl:grid-cols-3">

        <div class="rounded-xl border border-border bg-surface p-5 xl:col-span-2">
            <div class="mb-5">
                <h3 class="font-bold">Grafik Pemasukan</h3>
                <p class="text-sm text-muted">Contoh pemasukan selama 7 hari terakhir</p>
            </div>

            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Pemasukan Hari Ini</p>
            <h3 class="mt-3 text-3xl font-bold">Rp0</h3>
            <div class="mt-5 border-t border-border pt-4">
                <p class="text-sm text-muted">Total pembayaran diterima</p>
                <p class="mt-2 font-semibold">Rp12.500.000</p>
            </div>
        </div>

    </div>

    <!-- Servis terbaru -->
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
                    <tr class="border-t border-border">
                        <td class="px-5 py-4 font-medium">SRV-001</td>
                        <td class="px-5 py-4">Budi</td>
                        <td class="px-5 py-4">Canon EOS 600D</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">
                                Sedang Dikerjakan
                            </span>
                        </td>
                    </tr>
                    <tr class="border-t border-border">
                        <td class="px-5 py-4 font-medium">SRV-002</td>
                        <td class="px-5 py-4">Andi</td>
                        <td class="px-5 py-4">Sony A6000</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                Siap Diambil
                            </span>
                        </td>
                    </tr>
                    <tr class="border-t border-border">
                        <td class="px-5 py-4 font-medium">SRV-003</td>
                        <td class="px-5 py-4">Dina</td>
                        <td class="px-5 py-4">Nikon D5600</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                Menunggu Persetujuan
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@script
<script>
    const canvas = document.getElementById('revenueChart');

    if (canvas && !canvas.chartInstance) {
        canvas.chartInstance = new Chart(canvas, {
            type: 'line',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets: [{
                    label: 'Pemasukan',
                    data: [250000, 500000, 350000, 800000, 600000, 950000, 700000],
                    borderColor: '#171717',
                    backgroundColor: 'rgba(23, 23, 23, 0.08)',
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
                            callback: value => 'Rp' + value.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    }
</script>
@endscript
