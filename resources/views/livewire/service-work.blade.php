<div class="space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('services.show', $visit->id) }}"
           class="text-sm text-muted hover:text-foreground">
            ← Kembali ke Detail Servis
        </a>

        <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Pengerjaan Servis</h1>
                <p class="mt-1 text-sm text-muted">
                    Nomor Servis: {{ $visit->service_number }}
                    &middot; {{ $visit->customer->name }}
                </p>
            </div>

            <div class="text-sm text-muted">
                Status Estimasi:
                <span class="font-medium text-foreground">{{ $visit->estimate_status_label }}</span>
            </div>
        </div>
    </div>

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

    @if ($visit->estimate_status !== \App\Models\ServiceVisit::ESTIMATE_APPROVED)
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800 dark:border-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">
            Estimasi belum disetujui. Status perangkat masih bisa dicatat sebagai diterima/diperiksa,
            tetapi pengerjaan, menunggu suku cadang, dan selesai hanya dapat dipilih setelah estimasi disetujui.
        </div>
    @endif

    @if ($visit->estimate_status === \App\Models\ServiceVisit::ESTIMATE_REJECTED)
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
            Estimasi ditolak. Seluruh perangkat ditandai tidak jadi diperbaiki dan tidak dapat diproses.
        </div>
    @endif

    <div class="space-y-6">
        @foreach ($visit->devices as $device)
            <div wire:key="work-device-{{ $device->id }}" class="rounded-xl border border-border bg-surface p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold">
                            Perangkat {{ $loop->iteration }} — {{ $device->device_type }}
                            {{ $device->brand }} {{ $device->model }}
                        </h2>
                        <p class="mt-1 text-sm text-muted">Keluhan: {{ $device->complaint }}</p>
                    </div>

                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $device->status_badge_class }}">
                        {{ $device->status_label }}
                    </span>
                </div>

                <div class="mt-5 grid gap-4 lg:grid-cols-3">
                    <div class="lg:col-span-1">
                        <label class="mb-1 block text-sm font-medium">Status Pengerjaan</label>
                        <select wire:model="statuses.{{ $device->id }}"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2"
                            @disabled($device->status === \App\Models\ServiceDevice::STATUS_CANCELLED)>
                            @foreach ($workStatuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('statuses.' . $device->id)
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <label class="mb-1 mt-4 block text-sm font-medium">Catatan Progres</label>
                        <textarea wire:model="notes.{{ $device->id }}" rows="4"
                            placeholder="Contoh: Menunggu suku cadang flexible cable, estimasi tiba 3 hari."
                            class="w-full rounded-lg border border-border bg-background px-3 py-2"
                            @disabled($device->status === \App\Models\ServiceDevice::STATUS_CANCELLED)></textarea>
                        @error('notes.' . $device->id)
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <button
                            type="button"
                            wire:click="updateDeviceStatus({{ $device->id }})"
                            class="mt-4 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground"
                            @disabled($device->status === \App\Models\ServiceDevice::STATUS_CANCELLED)>
                            Simpan Progres
                        </button>
                    </div>

                    <div class="lg:col-span-2">
                        <h3 class="text-sm font-semibold">Riwayat Progres</h3>

                        <div class="mt-3 space-y-3">
                            @forelse ($device->histories as $history)
                                <div class="rounded-lg border border-border bg-background p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="text-sm font-medium">
                                            {{ $history->old_status_label }} → {{ $history->new_status_label }}
                                        </p>
                                        <p class="text-xs text-muted">
                                            {{ $history->created_at->translatedFormat('d F Y, H:i') }}
                                        </p>
                                    </div>

                                    @if ($history->note)
                                        <p class="mt-2 text-sm text-muted">{{ $history->note }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-muted">Belum ada riwayat progres untuk perangkat ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
