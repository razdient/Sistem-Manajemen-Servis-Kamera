<div class="space-y-6">
    {{-- Pelanggan --}}
    <div class="rounded-xl border border-border bg-surface p-5">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-bold">Informasi Pelanggan</h3>

            @unless ($showNewCustomerForm)
                <button
                    type="button"
                    wire:click="openNewCustomerForm"
                    class="inline-flex items-center gap-2 rounded-lg border border-border px-3 py-2 text-xs font-medium hover:bg-surface-secondary"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Pelanggan Baru
                </button>
            @endunless
        </div>

        <label class="mb-2 block text-sm font-medium">
            Pilih Pelanggan *
        </label>

        <select wire:model="customer_id"
            class="w-full rounded-lg border border-border bg-background px-3 py-2">
            <option value="">-- Pilih Pelanggan --</option>

            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}">
                    {{ $customer->name }} - {{ $customer->phone ?: 'tanpa nomor' }}
                </option>
            @endforeach
        </select>

        @error('customer_id')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror

        @if ($showNewCustomerForm)
            <div class="mt-4 rounded-xl border border-border bg-surface-secondary/60 p-4">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h4 class="text-sm font-semibold">Tambah Pelanggan Baru</h4>
                    <button type="button" wire:click="cancelNewCustomerForm"
                        class="text-xs font-medium text-muted hover:text-foreground">
                        Tutup
                    </button>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium">Nama Pelanggan *</label>
                        <input wire:model="newCustomerName" type="text"
                            placeholder="Nama lengkap pelanggan"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm">
                        @error('newCustomerName')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium">Nomor Telepon</label>
                        <input wire:model="newCustomerPhone" type="text"
                            placeholder="Contoh: 0812xxxx"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm">
                        @error('newCustomerPhone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium">Alamat</label>
                        <input wire:model="newCustomerAddress" type="text"
                            placeholder="Alamat pelanggan (opsional)"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm">
                        @error('newCustomerAddress')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-3 flex gap-2">
                    <button type="button" wire:click="saveNewCustomer"
                        wire:loading.attr="disabled" wire:loading.class="opacity-60 cursor-not-allowed"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-primary-foreground">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pelanggan
                    </button>

                    <button type="button" wire:click="cancelNewCustomerForm"
                        class="rounded-lg border border-border px-3 py-2 text-xs font-medium hover:bg-surface">
                        Batal
                    </button>
                </div>
            </div>
        @endif

        <label class="mb-2 mt-4 block text-sm font-medium">
            Catatan Kunjungan
        </label>

        <textarea wire:model="notes" rows="2" placeholder="Catatan tambahan untuk kunjungan ini..."
            class="w-full rounded-lg border border-border bg-background px-3 py-2"></textarea>
    </div>

    {{-- Perangkat --}}
    @foreach ($devices as $index => $device)
        <div wire:key="device-{{ $index }}"
            class="rounded-xl border border-border bg-surface p-5">

            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-bold">
                    Perangkat {{ $index + 1 }}
                </h3>

                @if (count($devices) > 1)
                    <button type="button"
                        wire:click="removeDevice({{ $index }})"
                        class="inline-flex items-center gap-1 text-sm font-medium text-red-500 hover:text-red-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                @endif
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm">Jenis Perangkat *</label>
                    <select wire:model="devices.{{ $index }}.device_type"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2">
                        <option>Kamera</option>
                        <option>Lensa</option>
                        <option>Flash</option>
                        <option>Tripod</option>
                        <option>Aksesori Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm">Merek *</label>
                    <input wire:model="devices.{{ $index }}.brand"
                        type="text" placeholder="Contoh: Canon"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2">
                    @error("devices.$index.brand")
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm">Model *</label>
                    <input wire:model="devices.{{ $index }}.model"
                        type="text" placeholder="Contoh: EOS 600D"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2">
                    @error("devices.$index.model")
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm">Kondisi Fisik</label>
                    <input wire:model="devices.{{ $index }}.physical_condition"
                        type="text" placeholder="Contoh: Ada lecet di bagian bawah"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm">Keluhan *</label>
                    <textarea wire:model="devices.{{ $index }}.complaint"
                        rows="2" placeholder="Jelaskan kerusakan perangkat..."
                        class="w-full rounded-lg border border-border bg-background px-3 py-2"></textarea>
                    @error("devices.$index.complaint")
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm">Kelengkapan</label>
                    <input wire:model="devices.{{ $index }}.accessories"
                        type="text" placeholder="Contoh: Baterai, charger, tas"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2">
                </div>
            </div>
        </div>
    @endforeach

    <div class="flex flex-wrap items-center justify-between gap-3">
        <button type="button" wire:click="addDevice"
            class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-surface-secondary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Perangkat
        </button>

        <button type="submit" wire:loading.attr="disabled"
            wire:loading.class="opacity-60 cursor-not-allowed"
            wire:target="saveIntake"
            class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Simpan Penerimaan Servis
        </button>
    </div>
</div>
