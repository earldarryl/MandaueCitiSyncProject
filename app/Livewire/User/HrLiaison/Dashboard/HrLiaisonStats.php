<?php

namespace App\Livewire\User\HrLiaison\Dashboard;

use Filament\Widgets\Widget;
use App\Models\Grievance;
use App\Models\HrLiaisonDepartment;
use App\Models\User;
use Carbon\Carbon;

class HrLiaisonStats extends Widget
{
    protected string $view = 'livewire.user.hr-liaison.dashboard.hr-liaison-stats';

    public $startDate;
    public $endDate;

    public $pending = 0;
    public $acknowledged = 0;
    public $inProgress = 0;
    public $escalated = 0;
    public $resolved = 0;
    public $unresolved = 0;
    public $closed = 0;
    public $overdue = 0;

    public $totalReceived = 0;
    public $latestGrievanceTicketId = null;
    public $citizenCount = 0;

    public $activeFellowHrLiaisons = 0;

    protected $listeners = [
        'dateRangeUpdated' => 'updateDateRange',
    ];

    public function mount($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? now()->format('Y-m-d');

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

        $baseQuery = Grievance::whereBetween('created_at', [$start, $end])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('hr_liaison_id', $userId);
            });

        $this->pending = (clone $baseQuery)->where('grievance_status', 'pending')->count();
        $this->acknowledged = (clone $baseQuery)->where('grievance_status', 'acknowledged')->count();
        $this->inProgress = (clone $baseQuery)->where('grievance_status', 'in_progress')->count();
        $this->escalated = (clone $baseQuery)->where('grievance_status', 'escalated')->count();
        $this->resolved = (clone $baseQuery)->where('grievance_status', 'resolved')->count();
        $this->unresolved = (clone $baseQuery)->where('grievance_status', 'unresolved')->count();
        $this->closed = (clone $baseQuery)->where('grievance_status', 'closed')->count();

        $this->overdue = (clone $baseQuery)
            ->whereNotIn('grievance_status', ['resolved', 'closed', 'unresolved'])
            ->whereRaw('DATE_ADD(created_at, INTERVAL processing_days DAY) < ?', [now()])
            ->count();

        $this->totalReceived = (clone $baseQuery)->count();

        $this->latestGrievanceTicketId = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->value('grievance_ticket_id');

        $this->citizenCount = (clone $baseQuery)
            ->distinct('user_id')
            ->count('user_id');

        $departmentIds = HrLiaisonDepartment::where('hr_liaison_id', $userId)
            ->pluck('department_id');

        $fiveMinutesAgo = now()->subMinutes(5);

        $this->activeFellowHrLiaisons = User::whereHas('departments', function ($q) use ($departmentIds) {
                $q->whereIn('departments.department_id', $departmentIds);
            })
            ->where('id', '!=', $userId)
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>', $fiveMinutesAgo)
            ->count();
    }
}
