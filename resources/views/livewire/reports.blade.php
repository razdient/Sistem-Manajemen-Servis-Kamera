<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Laporan</h1>
        <p class="mt-1 text-sm text-muted">Ringkasan pendapatan, pengeluaran, dan aktivitas servis.</p>
    </div>

    <div class="rounded-xl border border-border bg-surface p-5">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium">Dari Tanggal</label>
                <input wire:model.live="startDate" type="date"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Sampai Tanggal</label>
                <input wire:model.live="endDate" type="date"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2">
            </div>
            <div class="flex items-end">
                <p class="text-sm text-muted">
                    Laba kotor dihitung dari harga jual - harga modal pada estimasi yang disetujui dalam periode keputusan.
                </p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Pendapatan Diterima</p>
            <h3 class="mt-2 text-2xl font-bold">{{ $formatRupiah($totalIncome) }}</h3>
            <p class="mt-1 text-xs text-muted">Dari pembayaran pelanggan</p>
        </div>
        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Pengeluaran</p>
            <h3 class="mt-2 text-2xl font-bold">{{ $formatRupiah($totalExpenses) }}</h3>
            <p class="mt-1 text-xs text-muted">Pengeluaran toko</p>
        </div>
        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Penjualan Disetujui</p>
            <h3 class="mt-2 text-2xl font-bold">{{ $formatRupiah($grossSales) }}</h3>
            <p class="mt-1 text-xs text-muted">Modal: {{ $formatRupiah($grossCost) }}</p>
        </div>
        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Laba Kotor</p>
            <h3 class="mt-2 text-2xl font-bold {{ $grossProfit >= 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                {{ $formatRupiah($grossProfit) }}
            </h3>
            <p class="mt-1 text-xs text-muted">Harga jual - harga modal</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-surface">
        <div class="border-b border-border p-5">
            <h2 class="font-bold">Riwayat Servis</h2>
            <p class="text-sm text-muted">Kunjungan yang masuk dalam periode terpilih.</p>
        </div>

        <table class="w-full text-left text-sm">
            <thead class="bg-surface-secondary text-muted">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">No. Servis</th>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3">Status Estimasi</th>
                    <th class="px-4 py-3 text-right">Total Estimasi</th>
                    <th class="px-4 py-3 text-right">Dibayar</th>
                    <th class="px-4 py-3 text-right">Sisa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($visits as $visit)
                    @php
                        $paid = (int) $visit->payments->sum('amount');
                        $remaining = max($visit->estimate_total - $paid, 0);
                    @endphp
                    <tr wire:key="report-visit-{{ $visit->id }}">
                        <td class="px-4 py-3">{{ $visit->created_at->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('services.show', $visit->id) }}" class="hover:underline">
                                {{ $visit->service_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $visit->customer->name }}</td>
                        <td class="px-4 py-3">{{ $visit->estimate_status_label }}</td>
                        <td class="px-4 py-3 text-right">{{ $visit->formatted_estimate_total }}</td>
                        <td class="px-4 py-3 text-right">{{ \App\Models\ServiceItem::formatRupiah($paid) }}</td>
                        <td class="px-4 py-3 text-right">{{ \App\Models\ServiceItem::formatRupiah($remaining) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-muted">
                            Tidak ada data servis pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
