<?php

namespace Tests\Feature;

use App\Livewire\Expenses;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengeluaran_dapat_dicatat(): void
    {
        Livewire::test(Expenses::class)
            ->set('category', 'Operasional toko')
            ->set('description', 'Beli kertas nota')
            ->set('amount', 50000)
            ->set('expense_date', '2026-11-22')
            ->set('note', 'Toko A')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('expenses', [
            'category' => 'Operasional toko',
            'description' => 'Beli kertas nota',
            'amount' => 50000,
        ]);
    }

    public function test_pengeluaran_dapat_diedit_dan_dihapus(): void
    {
        $expense = Expense::create([
            'category' => 'Listrik',
            'description' => 'Token listrik',
            'amount' => 100000,
            'expense_date' => '2026-11-20',
        ]);

        Livewire::test(Expenses::class)
            ->call('edit', $expense->id)
            ->set('amount', 120000)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(120000, $expense->fresh()->amount);

        Livewire::test(Expenses::class)
            ->call('delete', $expense->id);

        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }
}
