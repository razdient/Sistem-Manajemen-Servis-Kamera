<?php

namespace Tests\Feature;

use App\Livewire\ServiceWork;
use App\Models\Customer;
use App\Models\ServiceDevice;
use App\Models\ServiceItem;
use App\Models\ServiceVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceWorkTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_perangkat_dapat_diperbarui_dan_mencatat_riwayat(): void
    {
        [$visit, $device] = $this->makeApprovedVisit();

        Livewire::test(ServiceWork::class, ['id' => $visit->id])
            ->set('statuses.'.$device->id, ServiceDevice::STATUS_IN_PROGRESS)
            ->set('notes.'.$device->id, 'Mulai pengerjaan')
            ->call('updateDeviceStatus', $device->id)
            ->assertHasNoErrors();

        $device->refresh();

        $this->assertSame(ServiceDevice::STATUS_IN_PROGRESS, $device->status);
        $this->assertDatabaseHas('service_histories', [
            'service_device_id' => $device->id,
            'old_status' => ServiceDevice::STATUS_RECEIVED,
            'new_status' => ServiceDevice::STATUS_IN_PROGRESS,
            'note' => 'Mulai pengerjaan',
        ]);
    }

    public function test_perangkat_tidak_bisa_dikerjakan_sebelum_estimasi_disetujui(): void
    {
        [$visit, $device] = $this->makeVisitWithDevice();

        Livewire::test(ServiceWork::class, ['id' => $visit->id])
            ->set('statuses.'.$device->id, ServiceDevice::STATUS_IN_PROGRESS)
            ->call('updateDeviceStatus', $device->id);

        $this->assertSame(ServiceDevice::STATUS_RECEIVED, $device->fresh()->status);
        $this->assertDatabaseCount('service_histories', 0);
    }

    public function test_status_menunggu_suku_cadang_dapat_dicatat(): void
    {
        [$visit, $device] = $this->makeApprovedVisit();

        Livewire::test(ServiceWork::class, ['id' => $visit->id])
            ->set('statuses.'.$device->id, ServiceDevice::STATUS_WAITING_PARTS)
            ->set('notes.'.$device->id, 'Menunggu flexible cable')
            ->call('updateDeviceStatus', $device->id)
            ->assertHasNoErrors();

        $this->assertSame(ServiceDevice::STATUS_WAITING_PARTS, $device->fresh()->status);
    }

    public function test_perangkat_yang_ditolak_tidak_dapat_diubah(): void
    {
        [$visit, $device] = $this->makeVisitWithDevice();
        $visit->update(['estimate_status' => ServiceVisit::ESTIMATE_REJECTED]);
        $device->update(['status' => ServiceDevice::STATUS_CANCELLED]);

        Livewire::test(ServiceWork::class, ['id' => $visit->id])
            ->set('statuses.'.$device->id, ServiceDevice::STATUS_IN_PROGRESS)
            ->call('updateDeviceStatus', $device->id);

        $this->assertSame(ServiceDevice::STATUS_CANCELLED, $device->fresh()->status);
        $this->assertDatabaseCount('service_histories', 0);
    }

    public function test_garansi_diatur_saat_semua_perangkat_selesai(): void
    {
        [$visit, $device] = $this->makeApprovedVisit();

        $this->travelTo(now()->startOfDay());

        Livewire::test(ServiceWork::class, ['id' => $visit->id])
            ->set('statuses.'.$device->id, ServiceDevice::STATUS_COMPLETED)
            ->call('updateDeviceStatus', $device->id)
            ->assertHasNoErrors();

        $visit->refresh();

        $this->assertNotNull($visit->ready_at);
        $this->assertNotNull($visit->warranty_started_at);
        $this->assertSame(
            $visit->ready_at->copy()->addMonthNoOverflow()->toDateString(),
            $visit->warranty_ended_at->toDateString()
        );

        // Jika status kembali bukan selesai, garansi dibersihkan.
        Livewire::test(ServiceWork::class, ['id' => $visit->id])
            ->set('statuses.'.$device->id, ServiceDevice::STATUS_IN_PROGRESS)
            ->call('updateDeviceStatus', $device->id)
            ->assertHasNoErrors();

        $visit->refresh();

        $this->assertNull($visit->ready_at);
        $this->assertNull($visit->warranty_ended_at);
    }

    private function makeApprovedVisit(): array
    {
        [$visit, $device] = $this->makeVisitWithDevice();

        $device->items()->create([
            'item_type' => ServiceItem::TYPE_SERVICE,
            'description' => 'Servis',
            'quantity' => 1,
            'unit_price' => 100000,
            'unit_cost' => 50000,
        ]);

        $visit->update([
            'estimate_status' => ServiceVisit::ESTIMATE_APPROVED,
            'estimated_at' => now(),
            'decision_at' => now(),
        ]);

        return [$visit, $device];
    }

    private function makeVisitWithDevice(): array
    {
        $customer = Customer::create(['name' => 'Pelanggan Uji']);

        $visit = ServiceVisit::create([
            'customer_id' => $customer->id,
            'service_number' => 'SRV-WORK-'.uniqid(),
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
