<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public $name = '';
    public $phone = '';
    public $address = '';
    public $notes = '';

    public $search = '';
    public $customerId = null;
    public $showForm = false;

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Customer::updateOrCreate(
            ['id' => $this->customerId],
            $validated
        );

        session()->flash('success', $this->customerId
            ? 'Data pelanggan berhasil diperbarui.'
            : 'Pelanggan berhasil ditambahkan.'
        );

        $this->resetForm();
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->notes = $customer->notes;

        $this->showForm = true;
    }

    public function delete($id)
    {
        Customer::findOrFail($id)->delete();

        session()->flash('success', 'Pelanggan berhasil diarsipkan.');
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'phone',
            'address',
            'notes',
            'customerId',
            'showForm',
        ]);

        $this->resetValidation();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.customers', [
            'customers' => Customer::query()
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('phone', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
        ])->layout('layouts.app', [
            'title' => 'Data Pelanggan',
        ]);
    }
}
