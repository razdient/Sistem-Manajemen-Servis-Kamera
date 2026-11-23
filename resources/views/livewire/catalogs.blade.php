<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Katalog Jasa & Suku Cadang</h1>
            <p class="mt-1 text-sm text-muted">
                Simpan daftar harga default untuk mempercepat pembuatan estimasi.
            </p>
        </div>

        <button
            wire:click="$set('showForm', true)"
            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground"
        >
            + Tambah Katalog
        </button>
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/30 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($showForm)
        <div class="rounded-xl border border-border bg-surface p-5">
            <h2 class="mb-4 font-bold">
                {{ $catalogId ? 'Edit Katalog' : 'Tambah Katalog' }}
            </h2>

            <form wire:submit="save" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Jenis *</label>
                        <select wire:model="item_type"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                            @foreach (\App\Models\ServiceItem::TYPE_LABELS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('item_type')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Kategori</label>
                        <input wire:model="category" type="text"
                            placeholder="Contoh: Pembersihan"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('category')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">Nama *</label>
                        <input wire:model="name" type="text"
                            placeholder="Contoh: Membersihkan sensor"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Harga Jual Default (Rp) *</label>
                        <input wire:model="default_price" type="number" min="0" step="1"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('default_price')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Harga Modal Default (Rp) *</label>
                        <input wire:model="default_cost" type="number" min="0" step="1"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('default_cost')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-medium">
                            <input wire:model="is_active" type="checkbox"
                                class="rounded border-border">
                            Aktif dan bisa dipilih saat membuat estimasi
                        </label>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-60 cursor-not-allowed"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground">
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

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Cari nama atau kategori..."
                class="w-full rounded-lg border border-border bg-surface px-4 py-2">
        </div>

        <div>
            <select wire:model.live="typeFilter"
                class="w-full rounded-lg border border-border bg-surface px-4 py-2">
                <option value="">Semua jenis</option>
                @foreach (\App\Models\ServiceItem::TYPE_LABELS as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-surface">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-secondary text-muted">
                <tr>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3 text-right">Harga Jual</th>
                    <th class="px-4 py-3 text-right">Harga Modal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-border">
                @forelse ($catalogs as $catalog)
                    <tr wire:key="catalog-{{ $catalog->id }}">
                        <td class="px-4 py-3">{{ $catalog->type_label }}</td>
                        <td class="px-4 py-3">{{ $catalog->category ?: '-' }}</td>
                        <td class="px-4 py-3 font-medium">{{ $catalog->name }}</td>
                        <td class="px-4 py-3 text-right">{{ $catalog->formatted_default_price }}</td>
                        <td class="px-4 py-3 text-right">{{ $catalog->formatted_default_cost }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-3 py-1 text-xs font-medium',
                                'bg-green-100 text-green-800' => $catalog->is_active,
                                'bg-surface-secondary text-muted' => ! $catalog->is_active,
                            ])>
                                {{ $catalog->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-3">
                                <button wire:click="edit({{ $catalog->id }})"
                                    class="font-medium text-blue-600">
                                    Edit
                                </button>

                                <button wire:click="toggleActive({{ $catalog->id }})"
                                    class="font-medium text-muted hover:text-foreground">
                                    {{ $catalog->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>

                                <button
                                    wire:click="delete({{ $catalog->id }})"
                                    wire:confirm="Hapus katalog ini? Data estimasi lama tidak akan berubah."
                                    class="font-medium text-red-600">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-muted">
                            Belum ada katalog. Tambahkan jasa atau suku cadang terlebih dahulu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-border p-4">
            {{ $catalogs->links() }}
        </div>
    </div>
</div>
