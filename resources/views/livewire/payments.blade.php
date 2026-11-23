<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Pembayaran</h1>
        <p class="mt-1 text-sm text-muted">Pantau tagihan dan pembayaran servis yang sudah disetujui.</p>
    </div>

    <div class="rounded-xl border border-border bg-surface p-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nomor servis atau pelanggan..."
            class="w-full rounded-lg border border-border bg-background px-4 py-2"
        >
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-surface">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-secondary text-muted">
                <tr>
                    <th class="px-4 py-3">Nomor Servis</th>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3 text-right">Total Biaya</th>
                    <th class="px-4 py-3 text-right">Sudah Dibayar</th>
                    <th class="px-4 py-3 text-right">Sisa Tagihan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($visits as $visit)
                    @php
                        $paid = (int) ($visit->total_paid_sum ?? 0);
                        $total = $visit->estimate_total;
                        $remaining = max($total - $paid, 0);
                        $isPaidOff = $total > 0 && $remaining === 0;
                    @endphp
                    <tr wire:key="payment-visit-{{ $visit->id }}">
                        <td class="px-4 py-3 font-medium">{{ $visit->service_number }}</td>
                        <td class="px-4 py-3">
                            <div>{{ $visit->customer->name }}</div>
                            <div class="text-xs text-muted">{{ $visit->customer->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-right">{{ \App\Models\ServiceItem::formatRupiah($total) }}</td>
                        <td class="px-4 py-3 text-right">{{ \App\Models\ServiceItem::formatRupiah($paid) }}</td>
                        <td class="px-4 py-3 text-right font-medium {{ $remaining > 0 ? 'text-red-700 dark:text-red-400' : 'text-green-700 dark:text-green-400' }}">
                            {{ \App\Models\ServiceItem::formatRupiah($remaining) }}
                        </td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-3 py-1 text-xs font-medium',
                                'bg-green-100 text-green-800' => $isPaidOff,
                                'bg-yellow-100 text-yellow-800' => ! $isPaidOff,
                            ])>
                                {{ $isPaidOff ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('services.payment', $visit->id) }}"
                               class="font-medium text-foreground underline underline-offset-4 hover:opacity-70">
                                Kelola
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-muted">
                            Belum ada data pembayaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-border p-4">
            {{ $visits->links() }}
        </div>
    </div>
</div>
