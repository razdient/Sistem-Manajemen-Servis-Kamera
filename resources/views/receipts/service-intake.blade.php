<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima - {{ $visit->service_number }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #000; background: {{ ($isPdf ?? false) ? '#fff' : '#eee' }}; margin: 0; padding: {{ ($isPdf ?? false) ? '0' : '30px' }}; }
        .receipt { max-width: 700px; margin: auto; padding: 35px; background: #fff; border: {{ ($isPdf ?? false) ? 'none' : '1px solid #ddd' }}; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 23px; }
        .header p { margin: 5px 0; font-size: 12px; }
        .title { text-align: center; margin: 20px 0; }
        .title h2 { margin: 0; font-size: 18px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { width: 50%; vertical-align: top; padding: 6px 8px 6px 0; }
        .label { font-size: 11px; color: #555; }
        .value { margin-top: 4px; font-size: 13px; font-weight: bold; }
        .device { border: 1px solid #000; padding: 15px; margin-bottom: 15px; }
        .device h3 { margin: 0 0 12px; font-size: 14px; }
        .device p { font-size: 12px; margin: 6px 0; }
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
<button class="print-button" onclick="window.print()"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 17H4a1 1 0 01-1-1v-5a2 2 0 012-2h14a2 2 0 012 2v5a1 1 0 01-1 1h-2M6 14h12v7H6v-7z"/></svg>Cetak Tanda Terima</button>
@endif
<div class="receipt">
    <div class="header">
        <img src="{{ ($isPdf ?? false) ? public_path('images/logo.jpg') : asset('images/logo.jpg') }}" alt="Dinamika Kamera" style="height: 56px; width: auto; margin-bottom: 8px;">
        <h1>DINAMIKA KAMERA</h1>
        <p>Purwokerto</p>
        <p>Bukti Penerimaan Perangkat Servis</p>
    </div>

    <div class="title">
        <h2>TANDA TERIMA SERVIS</h2>
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
                <div class="label">Jumlah Perangkat</div>
                <div class="value">{{ $visit->devices->count() }} perangkat</div>
            </td>
        </tr>
    </table>

    <h3>Rincian Perangkat</h3>
    @foreach ($visit->devices as $index => $device)
        <div class="device">
            <h3>Perangkat {{ $index + 1 }}: {{ $device->device_type }}</h3>
            <p><strong>Merek:</strong> {{ $device->brand }}</p>
            <p><strong>Model:</strong> {{ $device->model }}</p>
            <p><strong>Keluhan:</strong> {{ $device->complaint }}</p>
            <p><strong>Kondisi Fisik:</strong> {{ $device->physical_condition ?: '-' }}</p>
            <p><strong>Aksesori:</strong> {{ $device->accessories ?: '-' }}</p>
        </div>
    @endforeach

    @if ($visit->notes)
        <p><strong>Catatan Kunjungan:</strong> {{ $visit->notes }}</p>
    @endif

    <div class="footer">
        <strong>Catatan:</strong>
        <ul>
            <li>Tanda terima ini merupakan bukti penyerahan perangkat untuk diperiksa.</li>
            <li>Biaya perbaikan akan diinformasikan setelah pemeriksaan dan menunggu persetujuan pelanggan.</li>
            <li>Simpan tanda terima ini untuk pengambilan perangkat.</li>
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
