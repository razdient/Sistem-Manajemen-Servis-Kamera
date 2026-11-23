<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Pengeluaran Toko</h1>
            <p class="mt-1 text-sm text-muted">Catat pengeluaran operasional dan pembelian toko.</p>
        </div>

        <button
            wire:click="$set('showForm', true)"
            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground"
        >
            + Tambah Pengeluaran
        </button>
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/30 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-border bg-surface p-5">
        <p class="text-sm text-muted">Total Pengeluaran Bulan Ini</p>
        <h2 class="mt-2 text-2xl font-bold">{{ \App\Models\ServiceItem::formatRupiah((int) $totalThisMonth) }}</h2>
    </div>

    @if ($showForm)
        <div class="rounded-xl border border-border bg-surface p-5">
            <h2 class="mb-4 font-bold">{{ $expenseId ? 'Edit Pengeluaran' : 'Tambah Pengeluaran' }}</h2>

            <form wire:submit="save" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Kategori *</label>
                        <input wire:model="category" type="text" list="category-suggestions"
                            placeholder="Contoh: Pembelian suku cadang"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        <datalist id="category-suggestions">
                            @foreach ($categorySuggestions as $suggestion)
                                <option value="{{ $suggestion }}"></option>
                            @endforeach
                        </datalist>
                        @error('category')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Tanggal *</label>
                        <input wire:model="expense_date" type="date"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('expense_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">Deskripsi *</label>
                        <input wire:model="description" type="text"
                            placeholder="Contoh: Beli flexible cable Canon 600D"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Nominal (Rp) *</label>
                        <input wire:model="amount" type="number" min="1" step="1"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Catatan</label>
                        <input wire:model="note" type="text"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('note')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
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

    <div class="rounded-xl border border-border bg-surface p-4">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Cari kategori, deskripsi, atau catatan..."
            class="w-full rounded-lg border border-border bg-background px-4 py-2">
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-surface">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-secondary text-muted">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Deskripsi</th>
                    <th class="px-4 py-3 text-right">Nominal</th>
                    <th class="px-4 py-3">Catatan</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($expenses as $expense)
                    <tr wire:key="expense-{{ $expense->id }}">
                        <td class="px-4 py-3">{{ $expense->expense_date->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3">{{ $expense->category }}</td>
                        <td class="px-4 py-3">{{ $expense->description }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $expense->formatted_amount }}</td>
                        <td class="px-4 py-3">{{ $expense->note ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-3">
                                <button wire:click="edit({{ $expense->id }})" class="font-medium text-blue-600">
                                    Edit
                                </button>
                                <button
                                    wire:click="delete({{ $expense->id }})"
                                    wire:confirm="Hapus pengeluaran ini?"
                                    class="font-medium text-red-600">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-muted">
                            Belum ada pengeluaran tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-border p-4">
            {{ $expenses->links() }}
        </div>
    </div>
</div>
