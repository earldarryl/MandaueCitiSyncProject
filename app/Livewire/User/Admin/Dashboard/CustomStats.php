<?php

namespace App\Livewire\User\Admin\Dashboard;

use Filament\Widgets\Widget;
use App\Models\User;
use App\Models\Grievance;
use App\Models\Assignment;

class CustomStats extends Widget
{
    protected string $view = 'livewire.user.admin.dashboard.custom-stats';

    public $startDate;
    public $endDate;

    public $totalUsers;
    public $totalGrievances;
    public $totalAssignments;
    public $onlineUsers;

    // ✅ Grievance status counts
    public $pendingGrievances;
    public $rejectedGrievances;
    public $inProgressGrievances;
    public $resolvedGrievances;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        $this->calculateStats();
    }

        public function updatedStartDate($value)
    {
        $this->calculateStats();
        $this->dispatch('dateRangeUpdated', $value, $this->endDate);
    }

    public function updatedEndDate($value)
    {
        $this->calculateStats();
        $this->dispatch('dateRangeUpdated', $this->startDate, $value);
    }

    protected function calculateStats(): void
    {
        $start = $this->startDate . ' 00:00:00';
        $end = $this->endDate . ' 23:59:59';

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
