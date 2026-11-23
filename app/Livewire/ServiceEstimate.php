<?php

namespace App\Livewire;

use App\Models\ServiceCatalog;
use App\Models\ServiceDevice;
use App\Models\ServiceItem;
use App\Models\ServiceVisit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceEstimate extends Component
{
    public $serviceId;

    public $editingItemId = null;

    public $device_id = '';

    public $catalog_id = '';

    public $item_type = ServiceItem::TYPE_SERVICE;

    public $description = '';

    public $quantity = 1;

    public $unit_price = 0;

    public $unit_cost = 0;

    public function mount($id)
    {
        $this->serviceId = $id;
    }

    protected function rules(): array
    {
        return [
            'device_id' => [
                'required',
                Rule::exists('service_devices', 'id')
                    ->where('service_visit_id', $this->serviceId)
                    ->whereNull('deleted_at'),
            ],
            'catalog_id' => ['nullable', Rule::exists('service_catalogs', 'id')->where('is_active', true)],
            'item_type' => ['required', Rule::in(array_keys(ServiceItem::TYPE_LABELS))],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'integer', 'min:0'],
            'unit_cost' => ['required', 'integer', 'min:0'],
        ];
    }

    public function saveItem()
    {
        $visit = ServiceVisit::findOrFail($this->serviceId);

        if ($visit->isEstimateDecided()) {
            session()->flash('error', 'Estimasi sudah diputuskan dan tidak dapat diubah.');

            return;
        }

        $validated = $this->validate();

        $data = [
            'service_device_id' => $validated['device_id'],
            'item_type' => $validated['item_type'],
            'description' => $validated['description'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'unit_cost' => $validated['unit_cost'],
        ];

        if ($this->editingItemId) {
            $visit->items()->findOrFail($this->editingItemId)->update($data);

            session()->flash('success', 'Rincian estimasi berhasil diperbarui.');
        } else {
            ServiceItem::create($data);

            session()->flash('success', 'Rincian estimasi berhasil ditambahkan.');
        }

        $this->resetItemForm();
    }

    public function editItem($id)
    {
        $visit = ServiceVisit::findOrFail($this->serviceId);

        if ($visit->isEstimateDecided()) {
            session()->flash('error', 'Estimasi sudah diputuskan dan tidak dapat diubah.');

            return;
        }

        $item = $visit->items()->findOrFail($id);

        $this->editingItemId = $item->id;
        $this->device_id = $item->service_device_id;
        $this->catalog_id = '';
        $this->item_type = $item->item_type;
        $this->description = $item->description;
        $this->quantity = $item->quantity;
        $this->unit_price = $item->unit_price;
        $this->unit_cost = $item->unit_cost;

        $this->resetValidation();
    }

    public function updatedCatalogId($value)
    {
        if (! $value) {
            return;
        }

        $catalog = ServiceCatalog::where('is_active', true)->find($value);

        if (! $catalog) {
            $this->catalog_id = '';

            return;
        }

        $this->item_type = $catalog->item_type;
        $this->description = $catalog->name;
        $this->unit_price = $catalog->default_price;
        $this->unit_cost = $catalog->default_cost;
    }

    public function updatedItemType($value)
    {
        if (! $this->catalog_id) {
            return;
        }

        $catalog = ServiceCatalog::find($this->catalog_id);

        if (! $catalog || $catalog->item_type !== $value) {
            $this->catalog_id = '';
        }
    }

    public function deleteItem($id)
    {
        $visit = ServiceVisit::findOrFail($this->serviceId);

        if ($visit->isEstimateDecided()) {
            session()->flash('error', 'Estimasi sudah diputuskan dan tidak dapat diubah.');

            return;
        }

        $item = $visit->items()->findOrFail($id);

        $item->delete();

        if ($this->editingItemId === $item->id) {
            $this->resetItemForm();
        }

        session()->flash('success', 'Rincian estimasi berhasil dihapus.');
    }

    public function updateStatus($status)
    {
        $allowedStatuses = [
            ServiceVisit::ESTIMATE_WAITING_APPROVAL,
            ServiceVisit::ESTIMATE_APPROVED,
            ServiceVisit::ESTIMATE_REJECTED,
        ];

        if (! in_array($status, $allowedStatuses, true)) {
            return;
        }

        $visit = ServiceVisit::with('devices')->findOrFail($this->serviceId);

        if ($visit->isEstimateDecided()) {
            session()->flash('error', 'Estimasi sudah memiliki keputusan akhir.');

            return;
        }

        if ($visit->items()->count() === 0) {
            session()->flash('error', 'Tambahkan minimal satu rincian sebelum mengubah status estimasi.');

            return;
        }

        DB::transaction(function () use ($visit, $status) {
            $data = ['estimate_status' => $status];

            if ($status === ServiceVisit::ESTIMATE_WAITING_APPROVAL) {
                $data['estimated_at'] = now();
                $data['decision_at'] = null;
            } else {
                $data['estimated_at'] = $visit->estimated_at ?? now();
                $data['decision_at'] = now();
            }

            $visit->update($data);

            if ($status === ServiceVisit::ESTIMATE_REJECTED) {
                $visit->devices()->update(['status' => ServiceDevice::STATUS_CANCELLED]);
            }
        });

        $this->resetItemForm();

        session()->flash(
            'success',
            'Status estimasi sekarang: '.ServiceVisit::estimateStatusLabel($status).'.'
        );
    }

    public function resetItemForm()
    {
        $this->reset([
            'editingItemId',
            'device_id',
            'catalog_id',
            'item_type',
            'description',
            'quantity',
            'unit_price',
            'unit_cost',
        ]);

        $this->item_type = ServiceItem::TYPE_SERVICE;
        $this->quantity = 1;
        $this->unit_price = 0;
        $this->unit_cost = 0;

        $this->resetValidation();
    }

    public function render()
    {
        $visit = ServiceVisit::with([
            'customer',
            'devices.items',
        ])->findOrFail($this->serviceId);

        $catalogs = ServiceCatalog::where('is_active', true)
            ->orderBy('item_type')
            ->orderBy('name')
            ->get();

        return view('livewire.service-estimate', [
            'visit' => $visit,
            'devices' => $visit->devices,
            'catalogs' => $catalogs,
        ])->layout('layouts.app', [
            'title' => 'Pemeriksaan & Estimasi',
        ]);
    }
}
