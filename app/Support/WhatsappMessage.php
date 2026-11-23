<?php

namespace App\Support;

use App\Models\Payment;
use App\Models\ServiceItem;
use App\Models\ServiceVisit;

class WhatsappMessage
{
    public static function intakeReceipt(ServiceVisit $visit): string
    {
        $name = $visit->customer->name ?: 'Pelanggan';

        return implode("\n", [
            "Halo Bapak/Ibu {$name},",
            '',
            'Berikut kami kirimkan tanda terima penerimaan perangkat servis dari Dinamika Kamera.',
            '',
            "Nomor Servis: {$visit->service_number}",
            '',
            'Mohon simpan dokumen tersebut sebagai bukti penerimaan perangkat.',
            '',
            'Terima kasih telah mempercayakan servis perangkat Anda kepada kami.',
        ]);
    }

    public static function paymentReceipt(ServiceVisit $visit, ?Payment $payment = null): string
    {
        $name = $visit->customer->name ?: 'Pelanggan';
        $payment ??= $visit->payments()->latest('paid_at')->latest('id')->first();

        $currentPayment = $payment ? $payment->formatted_amount : ServiceItem::formatRupiah(0);

        return implode("\n", [
            "Halo Bapak/Ibu {$name},",
            '',
            'Berikut kami kirimkan bukti pembayaran servis dari Dinamika Kamera.',
            '',
            "Nomor Servis: {$visit->service_number}",
            '',
            'Total Biaya: '.$visit->formatted_estimate_total,
            'Pembayaran Saat Ini: '.$currentPayment,
            'Total Sudah Dibayar: '.$visit->formatted_total_paid,
            'Sisa Pembayaran: '.$visit->formatted_remaining_balance,
            '',
            'Mohon simpan bukti pembayaran ini sebagai arsip.',
            '',
            'Terima kasih.',
        ]);
    }
}
