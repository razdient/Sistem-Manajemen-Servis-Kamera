<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Livewire\ServiceIntake;
use App\Models\Customer;
use App\Models\ServiceVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardServiceIntakeTest extends TestCase
{
    use RefreshDatabase;

    public function test_servis_baru_dapat_dibuat_dari_modal_dashboard(): void
    {
        $component = Livewire::test(Dashboard::class)
            ->call('openIntakeModal')
            ->assertSet('showIntakeModal', true)
            ->call('openNewCustomerForm')
            ->assertSet('showNewCustomerForm', true)
            ->set('newCustomerName', 'Pelanggan Baru')
            ->set('newCustomerPhone', '08123456789')
            ->call('saveNewCustomer')
            ->assertHasNoErrors()
            ->assertSet('showNewCustomerForm', false)
            ->set('devices.0.brand', 'Canon')
            ->set('devices.0.model', 'EOS 600D')
            ->set('devices.0.complaint', 'Error 01')
            ->call('saveIntake')
            ->assertHasNoErrors()
            ->assertSet('showIntakeModal', false)
            ->assertSet('customer_id', '');

        $this->assertNotNull($component->get('savedServiceNumber'));
        $component->assertSee('Tanda Terima');

        $customer = Customer::where('name', 'Pelanggan Baru')->firstOrFail();

        $this->assertDatabaseHas('service_visits', [
            'customer_id' => $customer->id,
        ]);

        $visit = ServiceVisit::where('customer_id', $customer->id)->firstOrFail();

        $this->assertSame(1, $visit->devices()->count());
        $this->assertSame('Canon', $visit->devices()->first()->brand);
    }

    public function test_pelanggan_baru_tidak_tersimpan_tanpa_nama(): void
    {
        Livewire::test(Dashboard::class)
            ->call('openIntakeModal')
            ->call('openNewCustomerForm')
            ->set('newCustomerName', '')
            ->call('saveNewCustomer')
            ->assertHasErrors(['newCustomerName' => 'required']);

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_beberapa_perangkat_dapat_ditambahkan_dan_dihapus(): void
    {
        $component = Livewire::test(Dashboard::class)
            ->call('openIntakeModal')
            ->call('addDevice');

        $this->assertCount(2, $component->get('devices'));

        $component->call('removeDevice', 1);

        $this->assertCount(1, $component->get('devices'));
    }

    public function test_halaman_penerimaan_servis_tetap_dapat_menyimpan_servis(): void
    {
        $customer = Customer::create(['name' => 'Pelanggan Halaman']);

        Livewire::test(ServiceIntake::class)
            ->set('customer_id', (string) $customer->id)
            ->set('devices.0.brand', 'Nikon')
            ->set('devices.0.model', 'D5600')
            ->set('devices.0.complaint', 'Layar mati')
            ->call('saveIntake')
            ->assertHasNoErrors()
            ->assertSee('Servis berhasil dicatat');

        $this->assertDatabaseHas('service_visits', [
            'customer_id' => $customer->id,
        ]);
    }
}
