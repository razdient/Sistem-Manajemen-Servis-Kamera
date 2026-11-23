<div class="space-y-6">
    @php
        $formatRupiah = fn (int $value): string => \App\Models\ServiceItem::formatRupiah($value);
    @endphp

    {{-- Header --}}
    <div>
        <a href="{{ route('services.show', $visit->id) }}"
           class="text-sm text-muted hover:text-foreground">
            ← Kembali ke Detail Servis
        </a>

        <div class="mt-2">
            <h1 class="text-2xl font-bold">Pemeriksaan & Estimasi</h1>
            <p class="mt-1 text-sm text-muted">
                Nomor Servis: {{ $visit->service_number }}
                &middot; {{ $visit->customer->name }}
            </p>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/30 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-800 dark:bg-red-900/30 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- Ringkasan --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Total Estimasi</p>
            <h2 class="mt-2 text-2xl font-bold">{{ $visit->formatted_estimate_total }}</h2>
            <p class="mt-1 text-xs text-muted">Harga jual kepada pelanggan</p>
        </div>

        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Total Modal</p>
            <h2 class="mt-2 text-2xl font-bold">{{ $visit->formatted_estimate_cost_total }}</h2>
            <p class="mt-1 text-xs text-muted">Internal toko, tidak ditampilkan ke pelanggan</p>
        </div>

        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Status Estimasi</p>
            <div class="mt-3">
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $visit->estimate_status_badge_class }}">
                    {{ $visit->estimate_status_label }}
                </span>
            </div>

            @if ($visit->estimated_at)
                <p class="mt-3 text-xs text-muted">
                    Estimasi dibuat: {{ $visit->estimated_at->translatedFormat('d F Y, H:i') }}
                </p>
            @endif

            @if ($visit->decision_at)
                <p class="mt-1 text-xs text-muted">
                    Keputusan: {{ $visit->decision_at->translatedFormat('d F Y, H:i') }}
                </p>
            @endif
        </div>
    </div>

    {{-- Aksi status estimasi --}}
    <div class="rounded-xl border border-border bg-surface p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold">Keputusan Estimasi</h2>
                <p class="mt-1 text-sm text-muted">
                    Tentukan status setelah rincian biaya selesai diperiksa.
                </p>
            </div>

            @if (! $visit->isEstimateDecided())
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        wire:click="updateStatus('waiting_approval')"
                        class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-surface-secondary"
                    >
                        Ajukan Persetujuan
                    </button>

                    <button
                        type="button"
                        wire:click="updateStatus('approved')"
                        wire:confirm="Tandai estimasi ini sebagai disetujui pelanggan?"
                        class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800"
                    >
                        Disetujui
                    </button>

                    <button
                        type="button"
                        wire:click="updateStatus('rejected')"
                        wire:confirm="Tandai estimasi ini sebagai ditolak pelanggan? Seluruh perangkat pada kunjungan ini akan ditandai Tidak Jadi Diperbaiki."
                        class="rounded-lg bg-red-700 px-4 py-2 text-sm font-medium text-white hover:bg-red-800"
                    >
                        Ditolak
                    </button>
                </div>
            @endif
        </div>

        @if ($visit->isEstimateDecided())
            <p class="mt-3 text-sm text-muted">
                Estimasi sudah memiliki keputusan akhir dan rincian tidak dapat diubah.
                @if ($visit->estimate_status === \App\Models\ServiceVisit::ESTIMATE_REJECTED)
                    Kunjungan ini dicatat sebagai tidak jadi diperbaiki.
                @endif
            </p>
        @elseif ($visit->items->isEmpty())
            <p class="mt-3 text-sm text-muted">
                Tambahkan minimal satu rincian jasa atau suku cadang sebelum mengubah status.
            </p>
        @endif
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Daftar perangkat dan rincian --}}
        <div class="space-y-4 xl:col-span-2">
            @forelse ($devices as $device)
                <div wire:key="device-{{ $device->id }}" class="rounded-xl border border-border bg-surface">
                    <div class="border-b border-border p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h2 class="font-semibold">
                                    Perangkat {{ $loop->iteration }}:
                                    {{ $device->device_type }} {{ $device->brand }} {{ $device->model }}
                                </h2>
                                <p class="mt-1 text-sm text-muted">
                                    Keluhan: {{ $device->complaint }}
                                </p>
                            </div>

                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $device->status_badge_class }}">
                                {{ $device->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-surface-secondary text-muted">
                                <tr>
                                    <th class="px-4 py-3">Jenis</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3 text-right">Qty</th>
                                    <th class="px-4 py-3 text-right">Harga Jual</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                    <th class="px-4 py-3 text-right">Harga Modal</th>
                                    <th class="px-4 py-3 text-right">Total Modal</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border">
                                @forelse ($device->items as $item)
                                    <tr wire:key="item-{{ $item->id }}">
                                        <td class="px-4 py-3">{{ $item->type_label }}</td>
                                        <td class="px-4 py-3">{{ $item->description }}</td>
                                        <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right">{{ $item->formatted_unit_price }}</td>
                                        <td class="px-4 py-3 text-right font-medium">{{ $item->formatted_subtotal }}</td>
                                        <td class="px-4 py-3 text-right">{{ $item->formatted_unit_cost }}</td>
                                        <td class="px-4 py-3 text-right">{{ $item->formatted_total_cost }}</td>
                                        <td class="px-4 py-3">
                                            @if (! $visit->isEstimateDecided())
                                                <div class="flex gap-3">
                                                    <button
                                                        type="button"
                                                        wire:click="editItem({{ $item->id }})"
                                                        class="font-medium text-blue-600"
                                                    >
                                                        Edit
                                                    </button>

                                                    <button
                                                        type="button"
                                                        wire:click="deleteItem({{ $item->id }})"
                                                        wire:confirm="Hapus rincian ini?"
                                                        class="font-medium text-red-600"
                                                    >
                                                        Hapus
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-muted">
                                            Belum ada rincian untuk perangkat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap justify-end gap-6 border-t border-border p-4 text-sm">
                        <p>
                            Subtotal perangkat:
                            <span class="font-semibold">
                                {{ $formatRupiah($device->items->sum(fn ($item) => $item->subtotal)) }}
                            </span>
                        </p>
                        <p class="text-muted">
                            Modal:
                            <span class="font-semibold">
                                {{ $formatRupiah($device->items->sum(fn ($item) => $item->total_cost)) }}
                            </span>
                        </p>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-border bg-surface p-8 text-center text-muted">
                    Belum ada perangkat pada kunjungan ini.
                </div>
            @endforelse
        </div>

        {{-- Form rincian --}}
        <div class="xl:col-span-1">
            <div class="rounded-xl border border-border bg-surface p-5 xl:sticky xl:top-6">
                <h2 class="font-bold">
                    {{ $editingItemId ? 'Edit Rincian' : 'Tambah Rincian' }}
                </h2>
                <p class="mt-1 text-sm text-muted">
                    Harga modal hanya untuk internal toko.
                </p>

                @if ($visit->isEstimateDecided())
                    <div class="mt-5 rounded-lg bg-surface-secondary p-4 text-sm text-muted">
                        Estimasi sudah {{ strtolower($visit->estimate_status_label) }}.
                        Rincian tidak dapat ditambah atau diubah.
                    </div>
                @else
                    <form wire:submit="saveItem" class="mt-5 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Perangkat *</label>
                        <select wire:model="device_id"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                            <option value="">-- Pilih Perangkat --</option>
                            @foreach ($devices as $device)
                                <option value="{{ $device->id }}">
                                    Perangkat {{ $loop->iteration }} —
                                    {{ $device->brand }} {{ $device->model }}
                                </option>
                            @endforeach
                        </select>
                        @error('device_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Pilih dari Katalog (opsional)</label>
                        <select wire:model.live="catalog_id"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                            <option value="">-- Isi manual --</option>
                            @foreach ($catalogs as $catalog)
                                <option value="{{ $catalog->id }}">
                                    [{{ $catalog->type_label }}] {{ $catalog->name }}
                                    — {{ $catalog->formatted_default_price }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-muted">
                            Memilih katalog akan mengisi deskripsi dan harga default. Harga tetap bisa diubah.
                        </p>
                        @error('catalog_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Jenis *</label>
                        <select wire:model.live="item_type"
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
                        <label class="mb-1 block text-sm font-medium">Deskripsi *</label>
                        <input wire:model="description" type="text"
                            placeholder="Contoh: Perbaikan shutter"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Kuantitas *</label>
                        <input wire:model="quantity" type="number" min="1" step="1"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Harga Jual (Rp) *</label>
                        <input wire:model="unit_price" type="number" min="0" step="1"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('unit_price')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Harga Modal (Rp) *</label>
                        <input wire:model="unit_cost" type="number" min="0" step="1"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        @error('unit_cost')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-60 cursor-not-allowed"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground">
                            {{ $editingItemId ? 'Simpan Perubahan' : 'Tambah Rincian' }}
                        </button>

                        @if ($editingItemId)
                            <button type="button" wire:click="resetItemForm"
                                class="rounded-lg border border-border px-4 py-2 text-sm">
                                Batal
                            </button>
                        @endif
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
