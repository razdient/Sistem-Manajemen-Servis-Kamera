<?php

namespace App\Livewire;

use App\Models\ServiceVisit;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $visits = ServiceVisit::with(['customer', 'devices'])
            ->where(function ($query) {
                $query->where('service_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($customerQuery) {
                        $customerQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.service-list', [
            'visits' => $visits,
        ])->layout('layouts.app', [
            'title' => 'Daftar Servis',
        ]);
    }
}