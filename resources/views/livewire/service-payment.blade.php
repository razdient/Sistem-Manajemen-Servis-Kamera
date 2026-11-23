<div class="space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('services.show', $visit->id) }}"
           class="text-sm text-muted hover:text-foreground">
            ← Kembali ke Detail Servis
        </a>

        <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Pembayaran Servis</h1>
                <p class="mt-1 text-sm text-muted">
                    Nomor Servis: {{ $visit->service_number }}
                    &middot; {{ $visit->customer->name }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
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
                        'pdfUrl' => route('services.payment-receipt.pdf', $visit->id),
                        'whatsappNumber' => $visit->customer->whatsapp_number,
                        'message' => \App\Support\WhatsappMessage::paymentReceipt($visit),
                        'filename' => 'Bukti-Pembayaran-' . $visit->service_number . '.pdf',
                    ]) }})"
                    class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-surface-secondary disabled:cursor-not-allowed disabled:opacity-50"
                    @disabled(! $visit->customer->whatsapp_number)
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Kirim WhatsApp
                </button>
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
            Pembayaran hanya dapat dicatat setelah estimasi disetujui.
        </div>
    @endif

    {{-- Ringkasan --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Total Biaya</p>
            <h2 class="mt-2 text-2xl font-bold">{{ $visit->formatted_estimate_total }}</h2>
        </div>

        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Sudah Dibayar</p>
            <h2 class="mt-2 text-2xl font-bold text-green-700 dark:text-green-400">{{ $visit->formatted_total_paid }}</h2>
        </div>

        <div class="rounded-xl border border-border bg-surface p-5">
            <p class="text-sm text-muted">Sisa Tagihan</p>
            <h2 class="mt-2 text-2xl font-bold {{ $visit->remaining_balance > 0 ? 'text-red-700 dark:text-red-400' : 'text-green-700 dark:text-green-400' }}">
                {{ $visit->formatted_remaining_balance }}
            </h2>
            @if ($visit->isPaidOff())
                <p class="mt-1 text-xs text-green-700 dark:text-green-400 font-medium">Lunas</p>
            @endif
        </div>
    </div>

    @if ($visit->warranty_ended_at)
        <div class="rounded-xl border border-border bg-surface p-4 text-sm">
            <span class="font-medium">Garansi:</span>
            {{ $visit->warranty_started_at?->translatedFormat('d F Y') }} –
            {{ $visit->warranty_ended_at?->translatedFormat('d F Y') }}
            <span class="text-muted">(1 bulan sejak siap diambil)</span>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Form pembayaran --}}
        <div class="xl:col-span-1">
            <div class="rounded-xl border border-border bg-surface p-5">
                <h2 class="font-bold">Catat Pembayaran</h2>
                <p class="mt-1 text-sm text-muted">Riwayat pembayaran tidak akan dihapus.</p>

                @if ($visit->estimate_status === \App\Models\ServiceVisit::ESTIMATE_APPROVED && $visit->estimate_total > 0 && $visit->remaining_balance > 0)
                    <form wire:submit="savePayment" class="mt-5 space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Nominal (Rp) *</label>
                            <input wire:model="amount" type="number" min="1" max="{{ $visit->remaining_balance }}" step="1"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2">
                            @error('amount')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">Jenis Pembayaran *</label>
                            <select wire:model="payment_type"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2">
                                @foreach (\App\Models\Payment::TYPE_LABELS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('payment_type')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">Metode Pembayaran *</label>
                            <select wire:model="payment_method"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2">
                                @foreach (\App\Models\Payment::METHOD_LABELS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">Tanggal & Waktu *</label>
                            <input wire:model="paid_at" type="datetime-local"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2">
                            @error('paid_at')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">Catatan</label>
                            <textarea wire:model="note" rows="2"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2"></textarea>
                            @error('note')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-60 cursor-not-allowed"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground">
                            Simpan Pembayaran
                        </button>
                    </form>
                @elseif ($visit->isPaidOff())
                    <p class="mt-4 rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-300">
                        Tagihan sudah lunas. Riwayat pembayaran tetap tersimpan di bawah.
                    </p>
                @elseif ($visit->estimate_status === \App\Models\ServiceVisit::ESTIMATE_APPROVED && $visit->estimate_total <= 0)
                    <p class="mt-4 text-sm text-muted">
                        Total estimasi masih Rp 0. Tambahkan rincian estimasi terlebih dahulu.
                    </p>
                @else
                    <p class="mt-4 text-sm text-muted">
                        Form pembayaran akan tersedia setelah estimasi disetujui.
                    </p>
                @endif
            </div>
        </div>

        {{-- Riwayat pembayaran --}}
        <div class="xl:col-span-2">
            <div class="overflow-x-auto rounded-xl border border-border bg-surface">
                <div class="border-b border-border p-5">
                    <h2 class="font-bold">Riwayat Pembayaran</h2>
                </div>

                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-secondary text-muted">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Metode</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($visit->payments as $payment)
                            <tr wire:key="payment-{{ $payment->id }}">
                                <td class="px-4 py-3">{{ $payment->paid_at->translatedFormat('d F Y, H:i') }}</td>
                                <td class="px-4 py-3">{{ $payment->type_label }}</td>
                                <td class="px-4 py-3">{{ $payment->method_label }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ $payment->formatted_amount }}</td>
                                <td class="px-4 py-3">{{ $payment->note ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-muted">
                                    Belum ada pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
