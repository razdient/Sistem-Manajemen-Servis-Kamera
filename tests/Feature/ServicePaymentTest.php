<?php

namespace Tests\Feature;

use App\Livewire\ServicePayment;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\ServiceItem;
use App\Models\ServiceVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServicePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembayaran_dapat_dicatat_dan_sisa_tagihan_terhitung(): void
    {
        $visit = $this->makeApprovedVisit(total: 500000);

        Livewire::test(ServicePayment::class, ['id' => $visit->id])
            ->set('amount', 200000)
            ->set('payment_type', Payment::TYPE_DP)
            ->set('payment_method', Payment::METHOD_CASH)
            ->set('paid_at', '2026-11-22 10:00:00')
            ->call('savePayment')
            ->assertHasNoErrors();

        $visit->refresh();

        $this->assertSame(200000, $visit->total_paid);
        $this->assertSame(300000, $visit->remaining_balance);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_pembayaran_tidak_boleh_melebihi_sisa_tagihan(): void
    {
        $visit = $this->makeApprovedVisit(total: 100000);

        Livewire::test(ServicePayment::class, ['id' => $visit->id])
            ->set('amount', 150000)
            ->set('payment_type', Payment::TYPE_FULL)
            ->set('payment_method', Payment::METHOD_CASH)
            ->set('paid_at', '2026-11-22 10:00:00')
            ->call('savePayment')
            ->assertHasErrors(['amount']);

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_pembayaran_tidak_bisa_dicatat_jika_estimasi_belum_disetujui(): void
    {
        $visit = $this->makeVisitWithDevice(total: 100000);
        $visit->update(['estimate_status' => ServiceVisit::ESTIMATE_WAITING_APPROVAL]);

        Livewire::test(ServicePayment::class, ['id' => $visit->id])
            ->set('amount', 50000)
            ->set('payment_type', Payment::TYPE_DP)
            ->set('payment_method', Payment::METHOD_CASH)
            ->set('paid_at', '2026-11-22 10:00:00')
            ->call('savePayment');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_riwayat_pembayaran_tetap_tersimpan(): void
    {
        $visit = $this->makeApprovedVisit(total: 300000);

        Livewire::test(ServicePayment::class, ['id' => $visit->id])
            ->set('amount', 100000)
            ->set('payment_type', Payment::TYPE_DP)
            ->set('payment_method', Payment::METHOD_CASH)
            ->set('paid_at', '2026-11-22 10:00:00')
            ->call('savePayment')
            ->assertHasNoErrors();

        Livewire::test(ServicePayment::class, ['id' => $visit->id])
            ->set('amount', 200000)
            ->set('payment_type', Payment::TYPE_FULL)
            ->set('payment_method', Payment::METHOD_TRANSFER)
            ->set('paid_at', '2026-11-23 10:00:00')
            ->call('savePayment')
            ->assertHasNoErrors();

        $visit->refresh();

        $this->assertSame(2, $visit->payments()->count());
        $this->assertSame(300000, $visit->total_paid);
        $this->assertSame(0, $visit->remaining_balance);
        $this->assertTrue($visit->isPaidOff());
    }

    public function test_bukti_pembayaran_dapat_dicetak(): void
    {
        $visit = $this->makeApprovedVisit(total: 150000);

        Payment::create([
            'service_visit_id' => $visit->id,
            'amount' => 50000,
            'payment_type' => Payment::TYPE_DP,
            'payment_method' => Payment::METHOD_CASH,
            'paid_at' => now(),
        ]);

        $response = $this->get(route('services.payment-receipt', $visit->id));

        $response->assertOk();
        $response->assertSee('BUKTI PEMBAYARAN');
        $response->assertSee('Rp 150.000');
        $response->assertSee('Rp 50.000');
        $response->assertSee('Rp 100.000');
    }

    private function makeApprovedVisit(int $total): ServiceVisit
    {
        $visit = $this->makeVisitWithDevice($total);
        $visit->update([
            'estimate_status' => ServiceVisit::ESTIMATE_APPROVED,
            'estimated_at' => now(),
            'decision_at' => now(),
        ]);

        return $visit;
    }

    private function makeVisitWithDevice(int $total): ServiceVisit
    {
        $customer = Customer::create(['name' => 'Pelanggan Uji']);

        $visit = ServiceVisit::create([
            'customer_id' => $customer->id,
            'service_number' => 'SRV-PAY-'.uniqid(),
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
            'unit_price' => $total,
            'unit_cost' => 0,
        ]);

        return $visit;
    }
}
