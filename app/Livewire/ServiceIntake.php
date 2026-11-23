<?php

namespace App\Livewire;

use App\Livewire\Concerns\ManagesServiceIntake;
use Livewire\Component;

class ServiceIntake extends Component
{
    use ManagesServiceIntake;

    public function saveIntake(): void
    {
        $visit = $this->createIntakeVisit();

        session()->flash('success', 'Servis berhasil dicatat. Nomor servis: '.$visit->service_number);
    }

    public function render()
    {
        return view('livewire.service-intake', [
            'customers' => $this->intakeCustomers(),
        ])->layout('layouts.app', [
            'title' => 'Penerimaan Servis',
        ]);
    }
}
