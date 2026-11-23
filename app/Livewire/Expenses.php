<?php

namespace App\Livewire;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;

class Expenses extends Component
{
    use WithPagination;

    public $search = '';

    public $expenseId = null;

    public $category = '';

    public $description = '';

    public $amount = '';

    public $expense_date = '';

    public $note = '';

    public $showForm = false;

    public array $categorySuggestions = [
        'Pembelian suku cadang',
        'Operasional toko',
        'Listrik',
        'Sewa',
        'Peralatan',
        'Lainnya',
    ];

    public function mount()
    {
        $this->expense_date = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'expense_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        Expense::updateOrCreate(
            ['id' => $this->expenseId],
            $validated
        );

        session()->flash('success', $this->expenseId
            ? 'Pengeluaran berhasil diperbarui.'
            : 'Pengeluaran berhasil dicatat.'
        );

        $this->resetForm();
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);

        $this->expenseId = $expense->id;
        $this->category = $expense->category;
        $this->description = $expense->description;
        $this->amount = $expense->amount;
        $this->expense_date = $expense->expense_date->format('Y-m-d');
        $this->note = $expense->note;
        $this->showForm = true;

        $this->resetValidation();
    }

    public function delete($id)
    {
        Expense::findOrFail($id)->delete();

        session()->flash('success', 'Pengeluaran berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['expenseId', 'category', 'description', 'amount', 'note', 'showForm']);
        $this->expense_date = now()->format('Y-m-d');
        $this->resetValidation();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $expenses = Expense::query()
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('description', 'like', '%'.$this->search.'%')
                        ->orWhere('category', 'like', '%'.$this->search.'%')
                        ->orWhere('note', 'like', '%'.$this->search.'%');
                });
            })
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate(15);

        $totalThisMonth = Expense::whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        return view('livewire.expenses', [
            'expenses' => $expenses,
            'totalThisMonth' => $totalThisMonth,
        ])->layout('layouts.app', [
            'title' => 'Pengeluaran',
        ]);
    }
}
