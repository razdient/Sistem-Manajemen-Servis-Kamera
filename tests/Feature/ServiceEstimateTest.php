<?php

namespace Tests\Feature;

use App\Livewire\ServiceEstimate;
use App\Models\Customer;
use App\Models\ServiceDevice;
use App\Models\ServiceItem;
use App\Models\ServiceVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceEstimateTest extends TestCase
{
    use RefreshDatabase;

    public function test_rincian_estimasi_menghitung_subtotal_dan_total(): void
    {
        $customer = Customer::create([
            'name' => 'Pelanggan Uji',
            'phone' => '08123456789',
        ]);

        $visit = ServiceVisit::create([
            'customer_id' => $customer->id,
            'service_number' => 'SRV-TEST-0001',
        ]);

        $device = $visit->devices()->create([
            'device_type' => 'Kamera',
            'brand' => 'Canon',
            'model' => '600D',
            'complaint' => 'Error 01',
        ]);

        $service = $device->items()->create([
            'item_type' => ServiceItem::TYPE_SERVICE,
            'description' => 'Perbaikan shutter',
            'quantity' => 2,
            'unit_price' => 150000,
            'unit_cost' => 100000,
        ]);

        $device->items()->create([
            'item_type' => ServiceItem::TYPE_PART,
            'description' => 'Flexible cable',
            'quantity' => 1,
            'unit_price' => 50000,
            'unit_cost' => 30000,
        ]);

        $visit->refresh();

        $this->assertSame(2, $visit->items()->count());
        $this->assertSame(300000, $service->subtotal);
        $this->assertSame(200000, $service->total_cost);
        $this->assertSame(350000, $visit->estimate_total);
        $this->assertSame('Jasa', $service->type_label);
        $this->assertSame('Belum Diperiksa', $visit->estimate_status_label);
    }

    public function test_status_estimasi_dapat_diubah(): void
    {
        $customer = Customer::create(['name' => 'Pelanggan Uji']);

        $visit = ServiceVisit::create([
            'customer_id' => $customer->id,
            'service_number' => 'SRV-TEST-0002',
        ]);

        $visit->update([
            'estimate_status' => ServiceVisit::ESTIMATE_APPROVED,
            'decision_at' => now(),
        ]);

        $this->assertSame('Disetujui', $visit->fresh()->estimate_status_label);
        $this->assertNotNull($visit->fresh()->decision_at);
    }

    public function test_halaman_estimasi_dapat_menambah_mengubah_dan_menghapus_rincian(): void
    {
        [$visit, $device] = $this->makeVisitWithDevice();

        Livewire::test(ServiceEstimate::class, ['id' => $visit->id])
            ->set('device_id', $device->id)
            ->set('item_type', ServiceItem::TYPE_PART)
            ->set('description', 'LCD')
            ->set('quantity', 2)
            ->set('unit_price', 250000)
            ->set('unit_cost', 150000)
            ->call('saveItem')
            ->assertHasNoErrors();

        $item = $visit->items()->first();

        $this->assertNotNull($item);
        $this->assertSame(500000, $item->subtotal);

        Livewire::test(ServiceEstimate::class, ['id' => $visit->id])
            ->call('editItem', $item->id)
            ->assertSet('description', 'LCD')
            ->set('quantity', 3)
            ->call('saveItem')
            ->assertHasNoErrors();

        $this->assertSame(3, $item->fresh()->quantity);
        $this->assertSame(750000, $item->fresh()->subtotal);

        Livewire::test(ServiceEstimate::class, ['id' => $visit->id])
            ->call('deleteItem', $item->id)
            ->assertHasNoErrors();

        $this->assertSame(0, $visit->items()->count());
    }

    public function test_status_estimasi_dapat_diajukan_dan_disetujui(): void
    {
        [$visit, $device] = $this->makeVisitWithDevice();

        $device->items()->create([
            'item_type' => ServiceItem::TYPE_SERVICE,
            'description' => 'Pembersihan sensor',
            'quantity' => 1,
            'unit_price' => 100000,
            'unit_cost' => 20000,
        ]);

        Livewire::test(ServiceEstimate::class, ['id' => $visit->id])
            ->call('updateStatus', ServiceVisit::ESTIMATE_WAITING_APPROVAL)
            ->assertHasNoErrors();

        $visit->refresh();

        $this->assertSame(ServiceVisit::ESTIMATE_WAITING_APPROVAL, $visit->estimate_status);
        $this->assertNotNull($visit->estimated_at);
        $this->assertNull($visit->decision_at);

        Livewire::test(ServiceEstimate::class, ['id' => $visit->id])
            ->call('updateStatus', ServiceVisit::ESTIMATE_APPROVED)
            ->assertHasNoErrors();

        $visit->refresh();

        $this->assertSame(ServiceVisit::ESTIMATE_APPROVED, $visit->estimate_status);
        $this->assertNotNull($visit->decision_at);
    }

    public function test_estimasi_ditolak_menandai_perangkat_tidak_jadi_diperbaiki_dan_mengunci_rincian(): void
    {
        [$visit, $device] = $this->makeVisitWithDevice();

        $device->items()->create([
            'item_type' => ServiceItem::TYPE_SERVICE,
            'description' => 'Pembersihan sensor',
            'quantity' => 1,
            'unit_price' => 100000,
            'unit_cost' => 20000,
        ]);

        Livewire::test(ServiceEstimate::class, ['id' => $visit->id])
            ->call('updateStatus', ServiceVisit::ESTIMATE_REJECTED)
            ->assertHasNoErrors();

        $visit->refresh();
        $device->refresh();

        $this->assertSame(ServiceVisit::ESTIMATE_REJECTED, $visit->estimate_status);
        $this->assertSame(ServiceDevice::STATUS_CANCELLED, $device->status);
        $this->assertNotNull($visit->decision_at);

        Livewire::test(ServiceEstimate::class, ['id' => $visit->id])
            ->set('device_id', $device->id)
            ->set('item_type', ServiceItem::TYPE_SERVICE)
            ->set('description', 'Tes tambah setelah ditolak')
            ->set('quantity', 1)
            ->set('unit_price', 50000)
            ->set('unit_cost', 10000)
            ->call('saveItem');

        $this->assertSame(1, $visit->items()->count());
    }

    public function test_estimasi_yang_sudah_diputuskan_tidak_bisa_diubah_statusnya(): void
    {
        [$visit, $device] = $this->makeVisitWithDevice();

        $device->items()->create([
            'item_type' => ServiceItem::TYPE_SERVICE,
            'description' => 'Servis ringan',
            'quantity' => 1,
            'unit_price' => 100000,
            'unit_cost' => 20000,
        ]);

        $visit->update([
            'estimate_status' => ServiceVisit::ESTIMATE_APPROVED,
            'decision_at' => now(),
        ]);

        Livewire::test(ServiceEstimate::class, ['id' => $visit->id])
            ->call('updateStatus', ServiceVisit::ESTIMATE_REJECTED);

        $this->assertSame(ServiceVisit::ESTIMATE_APPROVED, $visit->fresh()->estimate_status);
    }

    private function makeVisitWithDevice(): array
    {
        $customer = Customer::create([
            'name' => 'Pelanggan Uji',
            'phone' => '08123456789',
        ]);

        $visit = ServiceVisit::create([
            'customer_id' => $customer->id,
            'service_number' => 'SRV-TEST-'.uniqid(),
        ]);

        $device = $visit->devices()->create([
            'device_type' => 'Kamera',
            'brand' => 'Canon',
            'model' => '600D',
            'complaint' => 'Error 01',
        ]);

        return [$visit, $device];
    }
}
