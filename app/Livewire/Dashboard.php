<?php

namespace App\Livewire;

use App\Livewire\Concerns\ManagesServiceIntake;
use App\Models\Payment;
use App\Models\ServiceDevice;
use App\Models\ServiceItem;
use App\Models\ServiceVisit;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    use ManagesServiceIntake;

    public bool $showIntakeModal = false;

    public ?int $savedVisitId = null;

    public ?string $savedServiceNumber = null;

    public function openIntakeModal(): void
    {
        $this->resetIntakeForm();
        $this->showIntakeModal = true;
        $this->savedVisitId = null;
        $this->savedServiceNumber = null;
    }

    public function closeIntakeModal(): void
    {
        $this->showIntakeModal = false;
        $this->resetValidation();
    }

    public function saveIntake(): void
    {
        $visit = $this->createIntakeVisit();

        $this->savedVisitId = $visit->id;
        $this->savedServiceNumber = $visit->service_number;
        $this->showIntakeModal = false;
    }

    public function render()
    {
        $activeStatuses = [
            ServiceDevice::STATUS_RECEIVED,
            ServiceDevice::STATUS_INSPECTING,
            ServiceDevice::STATUS_WAITING_APPROVAL,
            ServiceDevice::STATUS_IN_PROGRESS,
            ServiceDevice::STATUS_WAITING_PARTS,
        ];

        $totalVisits = ServiceVisit::count();

        $activeVisits = ServiceVisit::where('estimate_status', '!=', ServiceVisit::ESTIMATE_REJECTED)
            ->whereHas('devices', function ($query) use ($activeStatuses) {
                $query->whereIn('status', $activeStatuses);
            })
            ->count();

        $completedVisits = ServiceVisit::whereNotNull('ready_at')->count();
        $readyForPickup = $completedVisits;

        $todayIncome = (int) Payment::whereDate('paid_at', today())->sum('amount');
        $monthlyIncome = (int) Payment::whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        $unpaidBalance = (int) ServiceVisit::withSum('payments as total_paid_sum', 'amount')
            ->with('items')
            ->where('estimate_status', ServiceVisit::ESTIMATE_APPROVED)
            ->get()
            ->sum(function ($visit) {
                $paid = (int) ($visit->total_paid_sum ?? 0);

                return max($visit->estimate_total - $paid, 0);
            });

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $chartData[] = (int) Payment::whereDate('paid_at', $date)->sum('amount');
        }

        $recentVisits = ServiceVisit::with(['customer', 'devices'])
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.dashboard', [
            'totalVisits' => $totalVisits,
            'activeVisits' => $activeVisits,
            'completedVisits' => $completedVisits,
            'readyForPickup' => $readyForPickup,
            'todayIncome' => $todayIncome,
            'monthlyIncome' => $monthlyIncome,
            'unpaidBalance' => $unpaidBalance,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'recentVisits' => $recentVisits,
            'customers' => $this->intakeCustomers(),
            'formatRupiah' => fn (int $value) => ServiceItem::formatRupiah($value),
        ])->layout('layouts.app', [
            'title' => 'Dashboard',
        ]);
    }
}
