<?php

namespace App\Livewire\User\Admin\Dashboard;

use Filament\Widgets\Widget;
use App\Models\User;
use App\Models\Grievance;
use App\Models\Assignment;
use App\Models\Feedback;
use Carbon\Carbon;
use App\Models\Department;
class CustomStats extends Widget
{
    protected string $view = 'livewire.user.admin.dashboard.custom-stats';

    public $startDate;
    public $endDate;

    public $totalUsers = 0;
    public $citizenUsers = 0;
    public $hrLiaisonUsers = 0;
    public $onlineUsers = 0;

    public $totalAssignments = 0;
    public $assignmentsByDepartment = [];
    public $totalGrievances = 0;
    public $pendingGrievances = 0;
    public $unresolvedGrievances = 0;
    public $inProgressGrievances = 0;
    public $resolvedGrievances = 0;

    public $totalFeedbacks = 0;
    public $citizenFeedbacks = 0;
    public $hrLiaisonFeedbacks = 0;

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

        $this->totalUsers = User::whereBetween('created_at', [$start, $end])->count();
        $this->citizenUsers = User::whereBetween('created_at', [$start, $end])
            ->whereHas('roles', fn($q) => $q->where('name', 'citizen'))->count();
        $this->hrLiaisonUsers = User::whereBetween('created_at', [$start, $end])
            ->whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))->count();
        $this->onlineUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))->count();

        $this->totalAssignments = Assignment::whereBetween('assigned_at', [$start, $end])->count();

        $departments = Department::all();

        $this->assignmentsByDepartment = $departments->map(function ($dept) use ($start, $end) {
            $count = Assignment::where('department_id', $dept->department_id)
                ->whereBetween('assigned_at', [$start, $end])
                ->count();

            return [
                'department_name' => $dept->department_name,
                'total' => $count,
            ];
        });

        $this->totalGrievances = Grievance::whereBetween('created_at', [$start, $end])->count();
        $this->pendingGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'pending')->count();
        $this->unresolvedGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'unresolved')->count();
        $this->inProgressGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'in progress')->count();
        $this->resolvedGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'resolved')->count();

        $this->totalFeedbacks = Feedback::whereBetween('date', [$start, $end])->count();
        $this->citizenFeedbacks = Feedback::whereBetween('date', [$start, $end])
            ->whereHas('user.roles', fn($q) => $q->where('name', 'citizen'))->count();
        $this->hrLiaisonFeedbacks = Feedback::whereBetween('date', [$start, $end])
            ->whereHas('user.roles', fn($q) => $q->where('name', 'hr_liaison'))->count();
    }
}
