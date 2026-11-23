<?php

namespace App\Livewire\Concerns;

use App\Models\Customer;
use App\Models\ServiceVisit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait ManagesServiceIntake
{
    public string $customer_id = '';
    public string $notes = '';

    public array $devices = [
        [
            'device_type' => 'Kamera',
            'brand' => '',
            'model' => '',
            'complaint' => '',
            'physical_condition' => '',
            'accessories' => '',
        ],
    ];

    public bool $showNewCustomerForm = false;
    public string $newCustomerName = '';
    public string $newCustomerPhone = '';
    public string $newCustomerAddress = '';

    public function addDevice(): void
    {
        $this->devices[] = $this->blankDevice();
    }

    public function removeDevice(int $index): void
    {
        if (count($this->devices) <= 1) {
            return;
        }

        unset($this->devices[$index]);
        $this->devices = array_values($this->devices);
    }

    public function openNewCustomerForm(): void
    {
        $this->resetNewCustomerForm();
        $this->showNewCustomerForm = true;
    }

    public function cancelNewCustomerForm(): void
    {
        $this->resetNewCustomerForm();
    }

    public function saveNewCustomer(): void
    {
        $validated = $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerPhone' => 'nullable|string|max:30',
            'newCustomerAddress' => 'nullable|string',
        ], [], [
            'newCustomerName' => 'nama pelanggan',
            'newCustomerPhone' => 'nomor telepon',
            'newCustomerAddress' => 'alamat',
        ]);

        $customer = Customer::create([
            'name' => $validated['newCustomerName'],
            'phone' => $validated['newCustomerPhone'] ?: null,
            'address' => $validated['newCustomerAddress'] ?: null,
        ]);

        $this->customer_id = (string) $customer->id;
        $this->resetNewCustomerForm();
    }

    protected function createIntakeVisit(): ServiceVisit
    {
        $validated = $this->validate($this->intakeRules(), [], $this->intakeValidationAttributes());

        $visit = DB::transaction(function () use ($validated) {
            $visit = ServiceVisit::create([
                'customer_id' => $validated['customer_id'],
                'service_number' => $this->generateServiceNumber(),
                'notes' => $validated['notes'] ?: null,
            ]);

            foreach ($validated['devices'] as $device) {
                $visit->devices()->create($device);
            }

            return $visit;
        });

        $this->resetIntakeForm();

        return $visit;
    }

    protected function intakeRules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'notes' => 'nullable|string',
            'devices' => 'required|array|min:1',
            'devices.*.device_type' => 'required|string|max:100',
            'devices.*.brand' => 'required|string|max:100',
            'devices.*.model' => 'required|string|max:150',
            'devices.*.complaint' => 'required|string',
            'devices.*.physical_condition' => 'nullable|string',
            'devices.*.accessories' => 'nullable|string',
        ];
    }

    protected function intakeValidationAttributes(): array
    {
        return [
            'customer_id' => 'pelanggan',
            'notes' => 'catatan kunjungan',
            'devices.*.device_type' => 'jenis perangkat',
            'devices.*.brand' => 'merek',
            'devices.*.model' => 'model',
            'devices.*.complaint' => 'keluhan',
            'devices.*.physical_condition' => 'kondisi fisik',
            'devices.*.accessories' => 'kelengkapan',
        ];
    }

    protected function resetIntakeForm(): void
    {
        $this->reset(['customer_id', 'notes', 'showNewCustomerForm', 'newCustomerName', 'newCustomerPhone', 'newCustomerAddress']);
        $this->resetIntakeDevices();
        $this->resetValidation();
    }

    protected function resetIntakeDevices(): void
    {
        $this->devices = [$this->blankDevice()];
    }

    protected function resetNewCustomerForm(): void
    {
        $this->reset(['showNewCustomerForm', 'newCustomerName', 'newCustomerPhone', 'newCustomerAddress']);
        $this->resetValidation([
            'newCustomerName',
            'newCustomerPhone',
            'newCustomerAddress',
        ]);
    }

    protected function blankDevice(): array
    {
        return [
            'device_type' => 'Kamera',
            'brand' => '',
            'model' => '',
            'complaint' => '',
            'physical_condition' => '',
            'accessories' => '',
        ];
    }

    protected function intakeCustomers()
    {
        return Customer::orderBy('name')->get();
    }

    protected function generateServiceNumber(): string
    {
        return 'SRV-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
    }
}
