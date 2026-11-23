<div class="space-y-6">

    {{-- Header --}}
    <div>
        <a href="{{ route('services.index') }}"
           class="text-sm text-muted hover:text-foreground">
            ← Kembali ke Daftar Servis
        </a>

        <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Detail Servis</h1>
                <p class="mt-1 text-sm text-muted">
                    Nomor Servis: {{ $visit->service_number }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('services.estimate', $visit->id) }}"
                   class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground">
                    Pemeriksaan & Estimasi
                </a>

                <a href="{{ route('services.work', $visit->id) }}"
                   class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-surface-secondary">
                    Pengerjaan Servis
                </a>

                <a href="{{ route('services.payment', $visit->id) }}"
                   class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-surface-secondary">
                    Pembayaran
                </a>

                <button
                    onclick="window.open('{{ route('services.receipt', $visit->id) }}', '_blank')"
                    class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-surface-secondary"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 17H4a1 1 0 01-1-1v-5a2 2 0 012-2h14a2 2 0 012 2v5a1 1 0 01-1 1h-2M6 14h12v7H6v-7z"/></svg>
                    Cetak Tanda Terima
                </button>

                <button
                    onclick="window.open('{{ route('services.payment-receipt', $visit->id) }}', '_blank')"
                    class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-surface-secondary"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 17H4a1 1 0 01-1-1v-5a2 2 0 012-2h14a2 2 0 012 2v5a1 1 0 01-1 1h-2M6 14h12v7H6v-7z"/></svg>
                    Cetak Bukti Pembayaran
                </button>

                <button
                    type="button"
                    onclick="sendWhatsappDocument({{ \Illuminate\Support\Js::from([
                        'pdfUrl' => route('services.receipt.pdf', $visit->id),
                        'whatsappNumber' => $visit->customer->whatsapp_number,
                        'message' => \App\Support\WhatsappMessage::intakeReceipt($visit),
                        'filename' => 'Tanda-Terima-' . $visit->service_number . '.pdf',
                    ]) }})"
                    class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-surface-secondary disabled:cursor-not-allowed disabled:opacity-50"
                    @disabled(! $visit->customer->whatsapp_number)
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Kirim WhatsApp Tanda Terima
                </button>

                <button
                    type="button"
                    onclick="sendWhatsappDocument({{ \Illuminate\Support\Js::from([
                        'pdfUrl' => route('services.payment-receipt.pdf', $visit->id),
                        'whatsappNumber' => $visit->customer->whatsapp_number,
                        'message' => \App\Support\WhatsappMessage::paymentReceipt($visit),
                        'filename' => 'Bukti-Pembayaran-' . $visit->service_number . '.pdf',
                    ]) }})"
                    class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-surface-secondary disabled:cursor-not-allowed disabled:opacity-50"
                    @disabled(! $visit->customer->whatsapp_number)
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Kirim WhatsApp Bukti Bayar
                </button>
            </div>
        </div>
    </div>

    {{-- Informasi Pelanggan --}}
    <div class="rounded-xl border border-border bg-surface p-5">
        <h2 class="font-semibold">Informasi Pelanggan</h2>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs text-muted">Nama Pelanggan</p>
                <p class="mt-1 font-medium">{{ $visit->customer->name }}</p>
            </div>

            <div>
                <p class="text-xs text-muted">Nomor Telepon</p>
                <p class="mt-1 font-medium">
                    {{ $visit->customer->phone ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-muted">Tanggal Penerimaan</p>
                <p class="mt-1 font-medium">
                    {{ $visit->created_at->translatedFormat('d F Y, H:i') }}
                </p>
            </div>

            <div>
                <p class="text-xs text-muted">Catatan Kunjungan</p>
                <p class="mt-1 font-medium">{{ $visit->notes ?: '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Ringkasan Estimasi --}}
    <div class="rounded-xl border border-border bg-surface p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="font-semibold">Estimasi Biaya</h2>
                <div class="mt-2">
                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $visit->estimate_status_badge_class }}">
                        {{ $visit->estimate_status_label }}
                    </span>
                </div>
            </div>

            <p class="text-xl font-bold">{{ $visit->formatted_estimate_total }}</p>
        </div>

        <div class="mt-4 grid gap-4 text-sm md:grid-cols-3">
            <div>
                <p class="text-xs text-muted">Total Estimasi</p>
                <p class="mt-1 font-medium">{{ $visit->formatted_estimate_total }}</p>
            </div>
            <div>
                <p class="text-xs text-muted">Sudah Dibayar</p>
                <p class="mt-1 font-medium">{{ $visit->formatted_total_paid }}</p>
            </div>
            <div>
                <p class="text-xs text-muted">Sisa Tagihan</p>
                <p class="mt-1 font-medium {{ $visit->remaining_balance > 0 ? 'text-red-700 dark:text-red-400' : 'text-green-700 dark:text-green-400' }}">
                    {{ $visit->formatted_remaining_balance }}
                </p>
            </div>
            <div>
                <p class="text-xs text-muted">Siap Diambil</p>
                <p class="mt-1 font-medium">{{ $visit->ready_at?->translatedFormat('d F Y, H:i') ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-muted">Garansi Mulai</p>
                <p class="mt-1 font-medium">{{ $visit->warranty_started_at?->translatedFormat('d F Y, H:i') ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-muted">Garansi Berakhir</p>
                <p class="mt-1 font-medium">{{ $visit->warranty_ended_at?->translatedFormat('d F Y, H:i') ?: '-' }}</p>
            </div>
        </div>

        @if ($visit->estimate_status === \App\Models\ServiceVisit::ESTIMATE_REJECTED)
            <p class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-300">
                Estimasi ditolak. Kunjungan ini dicatat sebagai tidak jadi diperbaiki.
            </p>
        @endif
    </div>

    {{-- Daftar Perangkat --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">
            Perangkat yang Diservis ({{ $visit->devices->count() }})
        </h2>

        @foreach ($visit->devices as $index => $device)
            <div class="rounded-xl border border-border bg-surface p-5">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-semibold">
                        Perangkat {{ $index + 1 }} — {{ $device->device_type }}
                    </h3>

                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $device->status_badge_class }}">
                        {{ $device->status_label }}
                    </span>
                </div>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs text-muted">Merek</p>
                        <p class="mt-1">{{ $device->brand }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-muted">Model</p>
                        <p class="mt-1">{{ $device->model }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-muted">Keluhan</p>
                        <p class="mt-1">{{ $device->complaint }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-muted">Kondisi Fisik</p>
                        <p class="mt-1">
                            {{ $device->physical_condition ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-muted">Aksesori</p>
                        <p class="mt-1">{{ $device->accessories ?: '-' }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
