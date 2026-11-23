<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\ServiceItem;
use App\Models\ServiceVisit;
use App\Support\WhatsappMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_nomor_whatsapp_dinormalisasi_ke_format_indonesia(): void
    {
        $this->assertSame('6289666105232', (new Customer(['phone' => '089666105232']))->whatsapp_number);
        $this->assertSame('6281327004441', (new Customer(['phone' => '+62 813-2700-4441']))->whatsapp_number);
        $this->assertNull((new Customer(['phone' => '']))->whatsapp_number);
    }

    public function test_pesan_whatsapp_menggunakan_data_servis(): void
    {
        $visit = $this->makeVisit();

        $intakeMessage = WhatsappMessage::intakeReceipt($visit);
        $paymentMessage = WhatsappMessage::paymentReceipt($visit);

        $this->assertStringContainsString($visit->service_number, $intakeMessage);
        $this->assertStringContainsString('Dinamika Kamera', $intakeMessage);
        $this->assertStringContainsString('Total Biaya: '.$visit->formatted_estimate_total, $paymentMessage);
        $this->assertStringContainsString('Sisa Pembayaran: '.$visit->formatted_remaining_balance, $paymentMessage);
    }

    public function test_pdf_tanda_terima_dapat_diunduh(): void
    {
        $visit = $this->makeVisit();

        $response = $this->get(route('services.receipt.pdf', $visit->id));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('Tanda-Terima-'.$visit->service_number, (string) $response->headers->get('content-disposition'));
    }

    public function test_pdf_bukti_pembayaran_dapat_diunduh(): void
    {
        $visit = $this->makeVisit();

        Payment::create([
            'service_visit_id' => $visit->id,
            'amount' => 50000,
            'payment_type' => Payment::TYPE_DP,
            'payment_method' => Payment::METHOD_CASH,
            'paid_at' => now(),
        ]);

        $response = $this->get(route('services.payment-receipt.pdf', $visit->id));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('Bukti-Pembayaran-'.$visit->service_number, (string) $response->headers->get('content-disposition'));
    }

    private function makeVisit(): ServiceVisit
    {
        $customer = Customer::create([
            'name' => 'Pelanggan Uji',
            'phone' => '08123456789',
        ]);

        $visit = ServiceVisit::create([
            'customer_id' => $customer->id,
            'service_number' => 'SRV-WA-'.uniqid(),
            'estimate_status' => ServiceVisit::ESTIMATE_APPROVED,
        ]);

        $device = $visit->devices()->create([
            'device_type' => 'Kamera',
            'brand' => 'Canon',
            'model' => '600D',
            'complaint' => 'Error 01',
        ]);

        $device->items()->create([
            'item_type' => ServiceItem::TYPE_SERVICE,
            'description' => 'Servis',
            'quantity' => 1,
            'unit_price' => 150000,
            'unit_cost' => 50000,
        ]);

        return $visit;
    }
}
