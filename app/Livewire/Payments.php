<?php

namespace App\Livewire;

use App\Models\ServiceVisit;
use Livewire\Component;
use Livewire\WithPagination;

class Payments extends Component
{
    use WithPagination;

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $visits = ServiceVisit::with(['customer', 'items'])
            ->withSum('payments as total_paid_sum', 'amount')
            ->where(function ($query) {
                $query->where('estimate_status', ServiceVisit::ESTIMATE_APPROVED)
                    ->orWhereHas('payments');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('service_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('customer', function ($customerQuery) {
                            $customerQuery->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('phone', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.payments', [
            'visits' => $visits,
        ])->layout('layouts.app', [
            'title' => 'Pembayaran',
        ]);
    }
}
