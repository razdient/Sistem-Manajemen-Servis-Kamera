<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Daftar Servis</h1>
        <p class="mt-1 text-sm text-muted">
            Kelola dan pantau seluruh kunjungan servis.
        </p>
    </div>

    <div class="rounded-xl border border-border bg-surface p-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nomor servis atau pelanggan..."
            class="w-full rounded-lg border border-border bg-surface-secondary px-4 py-2 text-foreground outline-none focus:ring-2 focus:ring-primary"
        >
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-surface">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-secondary text-muted">
                <tr>
                    <th class="px-4 py-3">Nomor Servis</th>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3">Perangkat</th>
                    <th class="px-4 py-3">Tanggal Masuk</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-border">
                @forelse ($visits as $visit)
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            {{ $visit->service_number }}
                        </td>

                        <td class="px-4 py-3">
                            <div>{{ $visit->customer->name }}</div>
                            <div class="text-xs text-muted">
                                {{ $visit->customer->phone }}
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            {{ $visit->devices->count() }} perangkat
                        </td>

                        <td class="px-4 py-3">
                            {{ $visit->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="rounded-full bg-surface-secondary px-3 py-1 text-xs">
                                {{ $visit->status_labels }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <a href="{{ route('services.show', $visit->id) }}"
                            class="font-medium text-foreground underline underline-offset-4 hover:opacity-70">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-muted">
                            Belum ada data servis.
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