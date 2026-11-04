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
                    <button type="submit"
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
                    <th class="px-5 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($customers as $customer)
                    <tr class="border-t border-border">
                        <td class="px-5 py-4 font-medium">{{ $customer->name }}</td>
                        <td class="px-5 py-4">{{ $customer->phone ?: '-' }}</td>
                        <td class="px-5 py-4">{{ $customer->address ?: '-' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex gap-3">
                                <button wire:click="edit({{ $customer->id }})"
                                    class="font-medium text-blue-600">
                                    Edit
                                </button>

                                <button
                                    wire:click="delete({{ $customer->id }})"
                                    wire:confirm="Yakin ingin mengarsipkan pelanggan ini?"
                                    class="font-medium text-red-600">
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
