<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\ServiceVisit;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServicePayment extends Component
{
    public $serviceId;

    public $amount = '';

    public $payment_type = Payment::TYPE_DP;

    public $payment_method = Payment::METHOD_CASH;

    public $paid_at = '';

    public $note = '';

    public function mount($id)
    {
        $this->serviceId = $id;
        $this->paid_at = now()->format('Y-m-d\TH:i');
    }

    protected function rules(): array
    {
        $visit = ServiceVisit::with('payments')->findOrFail($this->serviceId);
        $remaining = $visit->remaining_balance;

        return [
            'amount' => ['required', 'integer', 'min:1', 'max:'.$remaining],
            'payment_type' => ['required', Rule::in(array_keys(Payment::TYPE_LABELS))],
            'payment_method' => ['required', Rule::in(array_keys(Payment::METHOD_LABELS))],
            'paid_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function savePayment()
    {
        $visit = ServiceVisit::with('payments')->findOrFail($this->serviceId);

        if ($visit->estimate_status !== ServiceVisit::ESTIMATE_APPROVED) {
            session()->flash('error', 'Pembayaran hanya dapat dicatat untuk estimasi yang sudah disetujui.');

            return;
        }

        if ($visit->remaining_balance <= 0) {
            session()->flash('error', 'Tagihan servis ini sudah lunas.');

            return;
        }

        $validated = $this->validate();

        Payment::create([
            'service_visit_id' => $visit->id,
            'amount' => $validated['amount'],
            'payment_type' => $validated['payment_type'],
            'payment_method' => $validated['payment_method'],
            'paid_at' => $validated['paid_at'],
            'note' => $validated['note'] ?: null,
        ]);

        $this->reset(['amount', 'note']);
        $this->payment_type = Payment::TYPE_DP;
        $this->payment_method = Payment::METHOD_CASH;
        $this->paid_at = now()->format('Y-m-d\TH:i');

        session()->flash('success', 'Pembayaran berhasil dicatat.');
    }

    public function render()
    {
        $visit = ServiceVisit::with([
            'customer',
            'devices',
            'payments',
        ])->findOrFail($this->serviceId);

        return view('livewire.service-payment', [
            'visit' => $visit,
        ])->layout('layouts.app', [
            'title' => 'Pembayaran Servis',
        ]);
    }
}
