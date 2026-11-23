<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\ServiceItem;
use App\Models\ServiceVisit;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Reports extends Component
{
    public $startDate;

    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $totalIncome = (int) Payment::whereBetween('paid_at', [$start, $end])->sum('amount');

        $totalExpenses = (int) Expense::whereBetween('expense_date', [
            $start->toDateString(),
            $end->toDateString(),
        ])->sum('amount');

        $approvedVisits = ServiceVisit::with('items')
            ->where('estimate_status', ServiceVisit::ESTIMATE_APPROVED)
            ->whereBetween('decision_at', [$start, $end])
            ->get();

        $grossSales = (int) $approvedVisits->sum(fn ($visit) => $visit->estimate_total);
        $grossCost = (int) $approvedVisits->sum(fn ($visit) => $visit->estimate_cost_total);
        $grossProfit = $grossSales - $grossCost;

        $visits = ServiceVisit::with(['customer', 'devices', 'payments'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        return view('livewire.reports', [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'grossSales' => $grossSales,
            'grossCost' => $grossCost,
            'grossProfit' => $grossProfit,
            'visits' => $visits,
            'formatRupiah' => fn (int $value) => ServiceItem::formatRupiah($value),
        ])->layout('layouts.app', [
            'title' => 'Laporan',
        ]);
    }
}
