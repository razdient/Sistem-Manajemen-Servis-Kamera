
<div>
    <!-- Statistik -->
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        @foreach ([
            ['Total Servis', '24'],
            ['Sedang Dikerjakan', '8'],
            ['Menunggu Persetujuan', '5'],
            ['Siap Diambil', '3']
        ] as $stat)

            <div class="rounded-xl border border-border bg-surface p-5">
                <p class="text-sm text-muted">{{ $stat[0] }}</p>
                <h3 class="mt-3 text-3xl font-bold">{{ $stat[1] }}</h3>
            </div>

        @endforeach

    </div>

    <!-- Pemasukan -->
    <div class="mb-6 rounded-xl border border-border bg-surface p-5">
        <p class="text-sm text-muted">Pemasukan Hari Ini</p>
        <h3 class="mt-2 text-3xl font-bold">Rp0</h3>
        <p class="mt-2 text-xs text-muted">
            Berdasarkan pembayaran yang diterima
        </p>
    </div>

    <!-- Servis Terbaru -->
    <div class="overflow-hidden rounded-xl border border-border bg-surface">

        <div class="border-b border-border p-5">
            <h3 class="font-bold">Servis Terbaru</h3>
            <p class="text-sm text-muted">
                Daftar kunjungan servis terakhir
            </p>
        </div>

        <div class="p-5 text-center text-sm text-muted">
            Belum ada data servis.
        </div>

    </div>
</div>
