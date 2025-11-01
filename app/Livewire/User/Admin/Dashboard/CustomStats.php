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

    // main stats
    public $totalUsers = 0;
    public $citizenUsers = 0;
    public $hrLiaisonUsers = 0;
    public $totalGrievances = 0;
    public $totalAssignments = 0;

    // other optional stats
    public $onlineUsers = 0;
    public $pendingGrievances = 0;
    public $unresolvedGrievances = 0;
    public $inProgressGrievances = 0;
    public $resolvedGrievances = 0;

    protected $listeners = ['dateRangeUpdated' => 'updateDateRange'];

    public function mount()
    {
        // default range: this month
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

        // --- USER STATS ---
        $this->totalUsers = User::whereBetween('created_at', [$start, $end])->count();

        $this->citizenUsers = User::whereBetween('created_at', [$start, $end])
            ->whereHas('roles', fn($q) => $q->where('name', 'citizen'))
            ->count();

        $this->hrLiaisonUsers = User::whereBetween('created_at', [$start, $end])
            ->whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->count();

        // --- GRIEVANCE STATS ---
        $this->totalGrievances = Grievance::whereBetween('created_at', [$start, $end])->count();

        $this->pendingGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'pending')
            ->count();

        $this->unresolvedGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'unresolved')
            ->count();

        $this->inProgressGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'in progress')
            ->count();

        $this->resolvedGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'resolved')
            ->count();

        // --- ASSIGNMENTS ---
        $this->totalAssignments = Assignment::whereBetween('created_at', [$start, $end])->count();

        // --- ONLINE USERS ---
        $this->onlineUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->count();
    }
}
