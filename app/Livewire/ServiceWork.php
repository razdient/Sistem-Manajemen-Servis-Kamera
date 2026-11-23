<?php

namespace App\Livewire;

use App\Models\ServiceDevice;
use App\Models\ServiceHistory;
use App\Models\ServiceVisit;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceWork extends Component
{
    public $serviceId;

    public array $statuses = [];

    public array $notes = [];

    public function mount($id)
    {
        $this->serviceId = $id;
        $this->syncDeviceInputs();
    }

    public function updateDeviceStatus($deviceId)
    {
        $visit = ServiceVisit::with('devices')->findOrFail($this->serviceId);
        $device = $visit->devices->firstWhere('id', (int) $deviceId);

        if (! $device) {
            session()->flash('error', 'Perangkat tidak ditemukan.');

            return;
        }

        $this->validate([
            "statuses.$deviceId" => ['required', Rule::in(array_keys(ServiceDevice::STATUS_LABELS))],
            "notes.$deviceId" => ['nullable', 'string', 'max:1000'],
        ]);

        $newStatus = $this->statuses[$deviceId];
        $note = trim((string) ($this->notes[$deviceId] ?? ''));

        if ($device->status === ServiceDevice::STATUS_CANCELLED) {
            session()->flash('error', 'Perangkat yang tidak jadi diperbaiki tidak dapat diubah statusnya.');

            return;
        }

        if ($newStatus === ServiceDevice::STATUS_CANCELLED) {
            session()->flash('error', 'Status "Tidak Jadi Diperbaiki" hanya ditetapkan melalui penolakan estimasi.');

            return;
        }

        $requiresApproval = [
            ServiceDevice::STATUS_IN_PROGRESS,
            ServiceDevice::STATUS_WAITING_PARTS,
            ServiceDevice::STATUS_COMPLETED,
        ];

        if (in_array($newStatus, $requiresApproval, true)
            && $visit->estimate_status !== ServiceVisit::ESTIMATE_APPROVED) {
            session()->flash('error', 'Estimasi harus disetujui sebelum perangkat dikerjakan atau diselesaikan.');

            return;
        }

        if ($device->status === $newStatus && $note === '') {
            session()->flash('error', 'Pilih status baru atau isi catatan progres.');

            return;
        }

        $oldStatus = $device->status;

        if ($device->status !== $newStatus) {
            $device->update(['status' => $newStatus]);
        }

        ServiceHistory::create([
            'service_device_id' => $device->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note !== '' ? $note : null,
        ]);

        $visit->load('devices');
        $visit->syncWarranty();

        $this->notes[$deviceId] = '';

        session()->flash('success', 'Progres perangkat berhasil diperbarui.');
    }

    private function syncDeviceInputs(): void
    {
        $visit = ServiceVisit::with('devices')->findOrFail($this->serviceId);

        foreach ($visit->devices as $device) {
            $this->statuses[$device->id] = $device->status;
            $this->notes[$device->id] = $this->notes[$device->id] ?? '';
        }
    }

    public function render()
    {
        $visit = ServiceVisit::with([
            'customer',
            'devices.histories',
        ])->findOrFail($this->serviceId);

        foreach ($visit->devices as $device) {
            if (! isset($this->statuses[$device->id])) {
                $this->statuses[$device->id] = $device->status;
            }
        }

        $workStatuses = collect(ServiceDevice::STATUS_LABELS)
            ->except([ServiceDevice::STATUS_CANCELLED])
            ->all();

        return view('livewire.service-work', [
            'visit' => $visit,
            'workStatuses' => $workStatuses,
        ])->layout('layouts.app', [
            'title' => 'Pengerjaan Servis',
        ]);
    }
}
