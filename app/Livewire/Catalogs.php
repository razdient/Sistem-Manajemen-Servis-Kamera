<?php

namespace App\Livewire;

use App\Models\ServiceCatalog;
use App\Models\ServiceItem;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Catalogs extends Component
{
    use WithPagination;

    public $search = '';

    public $typeFilter = '';

    public $catalogId = null;

    public $item_type = ServiceItem::TYPE_SERVICE;

    public $category = '';

    public $name = '';

    public $default_price = 0;

    public $default_cost = 0;

    public $is_active = true;

    public $showForm = false;

    public function save()
    {
        $validated = $this->validate([
            'item_type' => ['required', Rule::in(array_keys(ServiceItem::TYPE_LABELS))],
            'category' => ['nullable', 'string', 'max:100'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('service_catalogs', 'name')
                    ->where(fn ($query) => $query->where('item_type', $this->item_type))
                    ->ignore($this->catalogId),
            ],
            'default_price' => ['required', 'integer', 'min:0'],
            'default_cost' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        ServiceCatalog::updateOrCreate(
            ['id' => $this->catalogId],
            $validated
        );

        session()->flash('success', $this->catalogId
            ? 'Katalog berhasil diperbarui.'
            : 'Katalog berhasil ditambahkan.'
        );

        $this->resetForm();
    }

    public function edit($id)
    {
        $catalog = ServiceCatalog::findOrFail($id);

        $this->catalogId = $catalog->id;
        $this->item_type = $catalog->item_type;
        $this->category = $catalog->category;
        $this->name = $catalog->name;
        $this->default_price = $catalog->default_price;
        $this->default_cost = $catalog->default_cost;
        $this->is_active = $catalog->is_active;

        $this->showForm = true;
        $this->resetValidation();
    }

    public function toggleActive($id)
    {
        $catalog = ServiceCatalog::findOrFail($id);
        $catalog->update(['is_active' => ! $catalog->is_active]);
        $catalog->refresh();

        session()->flash('success', $catalog->is_active
            ? 'Katalog berhasil diaktifkan.'
            : 'Katalog berhasil dinonaktifkan.'
        );
    }

    public function delete($id)
    {
        ServiceCatalog::findOrFail($id)->delete();

        session()->flash('success', 'Katalog berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset([
            'catalogId',
            'item_type',
            'category',
            'name',
            'default_price',
            'default_cost',
            'is_active',
            'showForm',
        ]);

        $this->item_type = ServiceItem::TYPE_SERVICE;
        $this->default_price = 0;
        $this->default_cost = 0;
        $this->is_active = true;

        $this->resetValidation();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $catalogs = ServiceCatalog::query()
            ->when($this->search, function ($query) {
                $query->where(function ($innerQuery) {
                    $innerQuery->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('category', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('item_type', $this->typeFilter);
            })
            ->orderBy('item_type')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.catalogs', [
            'catalogs' => $catalogs,
        ])->layout('layouts.app', [
            'title' => 'Katalog Jasa & Suku Cadang',
        ]);
    }
}
