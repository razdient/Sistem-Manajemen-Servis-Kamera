<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold">Data Pelanggan</h2>
            <p class="mt-1 text-sm text-muted">Kelola informasi pelanggan toko.</p>
        </div>

        <button
            wire:click="$set('showForm', true)"
            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground"
        >
            + Tambah Pelanggan
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form -->
    @if ($showForm)
        <div class="mb-6 rounded-xl border border-border bg-surface p-5">
            <h3 class="mb-4 font-bold">
                {{ $customerId ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}
            </h3>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Nama Pelanggan *</label>
                    <input wire:model="name" type="text"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2">
                    @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Nomor Telepon</label>
                    <input wire:model="phone" type="text"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2">
                    @error('phone') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Alamat</label>
                    <textarea wire:model="address" rows="2"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2"></textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Catatan</label>
                    <textarea wire:model="notes" rows="2"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2"></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-60 cursor-not-allowed"
                        class="rounded-lg bg-primary px-4 py-2 text-sm text-primary-foreground">
                        Simpan
                    </button>

                    <button type="button" wire:click="resetForm"
                        class="rounded-lg border border-border px-4 py-2 text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Pencarian -->
    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Cari nama atau nomor telepon..."
            class="w-full rounded-lg border border-border bg-surface px-4 py-3">
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto rounded-xl border border-border bg-surface">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-secondary text-muted">
                <tr>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Telepon</th>
                    <th class="px-5 py-3">Alamat</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($customers as $customer)
                    <tr class="border-t border-border">
                        <td class="px-5 py-4 font-medium">{{ $customer->name }}</td>
                        <td class="px-5 py-4">{{ $customer->phone ?: '-' }}</td>
                        <td class="px-5 py-4">{{ $customer->address ?: '-' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                <button
                                    type="button"
                                    wire:click="edit({{ $customer->id }})"
                                    title="Edit data pelanggan"
                                    aria-label="Edit data pelanggan"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-surface px-2.5 py-1.5 text-xs font-medium text-foreground transition hover:bg-surface-secondary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    wire:click="delete({{ $customer->id }})"
                                    wire:confirm="Yakin ingin mengarsipkan pelanggan ini?"
                                    title="Arsipkan pelanggan"
                                    aria-label="Arsipkan pelanggan"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-300 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300 dark:hover:bg-red-900/30"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    Arsipkan
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-muted">
                            Belum ada data pelanggan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-border p-4">
            {{ $customers->links() }}
        </div>
    </div>
</div>
