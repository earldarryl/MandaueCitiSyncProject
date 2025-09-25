<?php

namespace App\Livewire\User\HrLiaison\Dashboard;

use Filament\Widgets\Widget;
use App\Models\Grievance;
use Carbon\Carbon;

class HrLiaisonStats extends Widget
{
    protected string $view = 'livewire.user.hr-liaison.dashboard.hr-liaison-stats';

    public $startDate;
    public $endDate;

    public $pending = 0;
    public $inProgress = 0;
    public $resolved = 0;
    public $closed = 0;
    public $overdue = 0;

    protected $listeners = ['dateRangeUpdated' => 'updateDateRange'];

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        $this->calculateStats();
    }

    public function updateDateRange($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;

        $this->calculateStats();
    }

    protected function calculateStats(): void
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $userId = auth()->id();

        // Base query filtered by HR liaison and date range
        $baseQuery = Grievance::whereBetween('created_at', [$start, $end])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('hr_liaison_id', $userId);
            });

        // Count statuses
        $this->pending = (clone $baseQuery)->where('grievance_status', 'pending')->count();
        $this->inProgress = (clone $baseQuery)->where('grievance_status', 'in_progress')->count();
        $this->resolved = (clone $baseQuery)->where('grievance_status', 'resolved')->count();
        $this->closed = (clone $baseQuery)->where('grievance_status', 'closed')->count();

        // Overdue = not resolved and past processing_days
        $this->overdue = (clone $baseQuery)
            ->where('grievance_status', '!=', 'resolved')
            ->whereRaw('DATE_ADD(created_at, INTERVAL processing_days DAY) < ?', [now()])
            ->count();
    }
}
