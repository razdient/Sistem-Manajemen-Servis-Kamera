<?php

namespace App\Livewire;

use App\Models\ServiceVisit;
use Livewire\Component;

class ServiceDetail extends Component
{
    public $serviceId;

    public function mount($id)
    {
        $this->serviceId = $id;
    }

    public function render()
    {
        $visit = ServiceVisit::with([
            'customer',
            'devices.items',
            'payments',
        ])->findOrFail($this->serviceId);

        return view('livewire.service-detail', [
            'visit' => $visit,
        ])->layout('layouts.app', [
            'title' => 'Detail Servis',
        ]);
    }
}
