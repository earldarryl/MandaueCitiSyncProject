<?php

namespace App\Livewire\User\Admin\Dashboard;

use Filament\Widgets\Widget;
use App\Models\User;
use App\Models\Grievance;
use App\Models\Assignment;
use Carbon\Carbon;

class CustomStats extends Widget
{
    protected string $view = 'livewire.user.admin.dashboard.custom-stats';

    public $startDate;
    public $endDate;

    public $totalUsers = 0;
    public $totalGrievances = 0;
    public $totalAssignments = 0;
    public $onlineUsers = 0;
    public $pendingGrievances = 0;
    public $rejectedGrievances = 0;
    public $inProgressGrievances = 0;
    public $resolvedGrievances = 0;

    protected $listeners = ['dateRangeUpdated' => 'updateDateRange'];

    public function mount()
    {
        // default: this month
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

        $this->totalUsers = User::whereBetween('created_at', [$start, $end])->count();
        $this->totalGrievances = Grievance::whereBetween('created_at', [$start, $end])->count();
        $this->totalAssignments = Assignment::whereBetween('created_at', [$start, $end])->count();

        $this->onlineUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->count();

        $this->pendingGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'pending')
            ->count();

        $this->rejectedGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'rejected')
            ->count();

        $this->inProgressGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'in progress')
            ->count();

        $this->resolvedGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'resolved')
            ->count();
    }
}
