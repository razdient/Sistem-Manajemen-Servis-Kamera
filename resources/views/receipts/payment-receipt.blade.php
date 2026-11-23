<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pembayaran - {{ $visit->service_number }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #000; background: {{ ($isPdf ?? false) ? '#fff' : '#eee' }}; margin: 0; padding: {{ ($isPdf ?? false) ? '0' : '30px' }}; }
        .receipt { max-width: 760px; margin: auto; padding: 35px; background: #fff; border: {{ ($isPdf ?? false) ? 'none' : '1px solid #ddd' }}; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 23px; }
        .header p { margin: 5px 0; font-size: 12px; }
        .title { text-align: center; margin: 20px 0; }
        .title h2 { margin: 0; font-size: 18px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { width: 50%; vertical-align: top; padding: 6px 8px 6px 0; }
        .label { font-size: 11px; color: #555; }
        .value { margin-top: 4px; font-size: 13px; font-weight: bold; }
        .section-title { margin: 20px 0 10px; font-size: 14px; border-bottom: 1px solid #000; padding-bottom: 6px; }
        table.data-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 8px; vertical-align: top; }
        table.data-table th { background: #f5f5f5; text-align: left; }
        .text-right { text-align: right; }
        .summary { margin-top: 20px; width: 320px; margin-left: auto; }
        .summary table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .summary td { border: 1px solid #000; padding: 8px; }
        .footer { border-top: 1px solid #000; margin-top: 25px; padding-top: 15px; font-size: 12px; line-height: 1.6; }
        .signature-table { width: 100%; margin-top: 35px; text-align: center; font-size: 12px; }
        .signature-table td { width: 50%; height: 100px; vertical-align: top; padding: 0 20px; }
        .print-button { display: block; margin: 0 auto 20px; padding: 10px 20px; background: #000; color: #fff; border: none; cursor: pointer; }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { max-width: none; border: none; padding: 10px; }
            .print-button { display: none; }
            @page { size: A4; margin: 15mm; }
        }
    </style>
</head>
<body>
@if (!($isPdf ?? false))
<button class="print-button" onclick="window.print()"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 17H4a1 1 0 01-1-1v-5a2 2 0 012-2h14a2 2 0 012 2v5a1 1 0 01-1 1h-2M6 14h12v7H6v-7z"/></svg>Cetak Bukti Pembayaran</button>
@endif
<div class="receipt">
    <div class="header">
        <img src="{{ ($isPdf ?? false) ? public_path('images/logo.jpg') : asset('images/logo.jpg') }}" alt="Dinamika Kamera" style="height: 56px; width: auto; margin-bottom: 8px;">
        <h1>DINAMIKA KAMERA</h1>
        <p>Purwokerto</p>
        <p>Bukti Pembayaran Servis</p>
    </div>

    <div class="title">
        <h2>BUKTI PEMBAYARAN</h2>
        <p>{{ $visit->service_number }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <div class="label">Nama Pelanggan</div>
                <div class="value">{{ $visit->customer->name }}</div>
            </td>
            <td>
                <div class="label">Nomor Telepon</div>
                <div class="value">{{ $visit->customer->phone ?? '-' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Tanggal Penerimaan</div>
                <div class="value">{{ $visit->created_at->format('d/m/Y H:i') }}</div>
            </td>
            <td>
                <div class="label">Status Estimasi</div>
                <div class="value">{{ $visit->estimate_status_label }}</div>
            </td>
        </tr>
    </table>

    <h3 class="section-title">Rincian Biaya</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Perangkat</th>
                <th>Jenis</th>
                <th>Deskripsi</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $hasItems = false; @endphp
            @foreach ($visit->devices as $device)
                @foreach ($device->items as $item)
                    @php $hasItems = true; @endphp
                    <tr>
                        <td>{{ $device->device_type }} {{ $device->brand }} {{ $device->model }}</td>
                        <td>{{ $item->type_label }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ $item->formatted_unit_price }}</td>
                        <td class="text-right">{{ $item->formatted_subtotal }}</td>
                    </tr>
                @endforeach
            @endforeach
            @if (! $hasItems)
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada rincian biaya.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Total Biaya</td>
                <td class="text-right"><strong>{{ $visit->formatted_estimate_total }}</strong></td>
            </tr>
            <tr>
                <td>Total Dibayar</td>
                <td class="text-right"><strong>{{ $visit->formatted_total_paid }}</strong></td>
            </tr>
            <tr>
                <td>Sisa Tagihan</td>
                <td class="text-right"><strong>{{ $visit->formatted_remaining_balance }}</strong></td>
            </tr>
        </table>
    </div>

    <h3 class="section-title">Riwayat Pembayaran</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Metode</th>
                <th class="text-right">Nominal</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visit->payments as $payment)
                <tr>
                    <td>{{ $payment->paid_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $payment->type_label }}</td>
                    <td>{{ $payment->method_label }}</td>
                    <td class="text-right">{{ $payment->formatted_amount }}</td>
                    <td>{{ $payment->note ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada pembayaran.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Garansi</h3>
    <table class="data-table">
        <tr>
            <td style="width: 220px;">Siap Diambil</td>
            <td>{{ $visit->ready_at?->format('d/m/Y H:i') ?: '-' }}</td>
        </tr>
        <tr>
            <td>Garansi Mulai</td>
            <td>{{ $visit->warranty_started_at?->format('d/m/Y H:i') ?: '-' }}</td>
        </tr>
        <tr>
            <td>Garansi Berakhir</td>
            <td>{{ $visit->warranty_ended_at?->format('d/m/Y H:i') ?: '-' }}</td>
        </tr>
    </table>

    <div class="footer">
        <strong>Catatan:</strong>
        <ul>
            <li>Bukti pembayaran ini merupakan dokumen transaksi servis.</li>
            <li>Garansi berlaku 1 bulan kalender sejak seluruh perangkat siap diambil.</li>
            <li>Simpan bukti ini untuk keperluan klaim garansi.</li>
        </ul>
    </div>

    <table class="signature-table">
        <tr>
            <td>
                Pelanggan,
                <br><br><br><br>
                (...........................)
            </td>
            <td>
                Petugas,
                <br><br><br><br>
                (...........................)
            </td>
        </tr>
    </table>
</div>
</body>
</html>
