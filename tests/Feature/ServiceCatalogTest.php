<?php

namespace Tests\Feature;

use App\Livewire\Catalogs;
use App\Livewire\ServiceEstimate;
use App\Models\Customer;
use App\Models\ServiceCatalog;
use App\Models\ServiceItem;
use App\Models\ServiceVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_katalog_dapat_dibuat_dan_dipakai_di_estimasi(): void
    {
        $catalog = ServiceCatalog::create([
            'item_type' => ServiceItem::TYPE_SERVICE,
            'category' => 'Pembersihan',
            'name' => 'Membersihkan sensor',
            'default_price' => 150000,
            'default_cost' => 50000,
            'is_active' => true,
        ]);

        [$visit, $device] = $this->makeVisitWithDevice();

        Livewire::test(ServiceEstimate::class, ['id' => $visit->id])
            ->set('device_id', $device->id)
            ->set('catalog_id', $catalog->id)
            ->assertSet('item_type', ServiceItem::TYPE_SERVICE)
            ->assertSet('description', 'Membersihkan sensor')
            ->assertSet('unit_price', 150000)
            ->assertSet('unit_cost', 50000)
            ->set('quantity', 2)
            ->call('saveItem')
            ->assertHasNoErrors();

        $item = $visit->items()->first();

        $this->assertNotNull($item);
        $this->assertSame(300000, $item->subtotal);
        $this->assertSame('Membersihkan sensor', $item->description);
    }

    public function test_halaman_katalog_dapat_menambah_data(): void
    {
        Livewire::test(Catalogs::class)
            ->set('showForm', true)
            ->set('item_type', ServiceItem::TYPE_PART)
            ->set('category', 'Suku Cadang')
            ->set('name', 'Flexible cable')
            ->set('default_price', 200000)
            ->set('default_cost', 120000)
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('service_catalogs', [
            'name' => 'Flexible cable',
            'item_type' => ServiceItem::TYPE_PART,
            'default_price' => 200000,
        ]);
    }

    private function makeVisitWithDevice(): array
    {
        $customer = Customer::create([
            'name' => 'Pelanggan Uji',
            'phone' => '08123456789',
        ]);

        $visit = ServiceVisit::create([
            'customer_id' => $customer->id,
            'service_number' => 'SRV-CAT-'.uniqid(),
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
