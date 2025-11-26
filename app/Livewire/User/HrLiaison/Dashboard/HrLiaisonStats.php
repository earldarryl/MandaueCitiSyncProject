<?php

namespace App\Livewire\User\HrLiaison\Dashboard;

use App\Models\Assignment;
use Filament\Widgets\Widget;
use App\Models\Grievance;
use App\Models\HrLiaisonDepartment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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
        $cacheKey = 'hr_liaison_stats_' . auth()->id() . '_' . $this->startDate . '_' . $this->endDate;

        $cachedStats = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
            $userId = auth()->id();

            $baseQuery = Grievance::whereBetween('created_at', [$start, $end])
                ->whereHas('assignments', function ($q) use ($userId) {
                    $q->where('hr_liaison_id', $userId);
                });

            $pending = (clone $baseQuery)->where('grievance_status', 'pending')->count();
            $acknowledged = (clone $baseQuery)->where('grievance_status', 'acknowledged')->count();
            $inProgress = (clone $baseQuery)->where('grievance_status', 'in_progress')->count();
            $escalated = (clone $baseQuery)->where('grievance_status', 'escalated')->count();
            $resolved = (clone $baseQuery)->where('grievance_status', 'resolved')->count();
            $unresolved = (clone $baseQuery)->where('grievance_status', 'unresolved')->count();
            $closed = (clone $baseQuery)->where('grievance_status', 'closed')->count();

            $overdue = (clone $baseQuery)
                ->whereNotIn('grievance_status', ['resolved', 'closed', 'unresolved'])
                ->whereRaw('DATE_ADD(created_at, INTERVAL processing_days DAY) < ?', [now()])
                ->count();

            $totalReceived = (clone $baseQuery)->count();

            $latestGrievanceTicketId = Assignment::where('hr_liaison_id', $userId)
                ->whereHas('grievance', function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                })
                ->select('grievance_id')
                ->distinct()
                ->orderByDesc('assigned_at')
                ->with(['grievance' => function ($q) {
                    $q->select('grievance_id', 'grievance_ticket_id');
                }])
                ->first()?->grievance?->grievance_ticket_id;

            $citizenCount = (clone $baseQuery)
                ->distinct('user_id')
                ->count('user_id');

            $departmentIds = HrLiaisonDepartment::where('hr_liaison_id', $userId)
                ->pluck('department_id');

            $fiveMinutesAgo = now()->subMinutes(5);

            $activeFellowHrLiaisons = User::whereHas('departments', function ($q) use ($departmentIds) {
                    $q->whereIn('departments.department_id', $departmentIds);
                })
                ->where('id', '!=', $userId)
                ->whereNotNull('last_seen_at')
                ->where('last_seen_at', '>', $fiveMinutesAgo)
                ->count();

            return compact(
                'pending', 'acknowledged', 'inProgress', 'escalated',
                'resolved', 'unresolved', 'closed', 'overdue',
                'totalReceived', 'latestGrievanceTicketId', 'citizenCount',
                'activeFellowHrLiaisons'
            );
        });

        $this->pending = $cachedStats['pending'];
        $this->acknowledged = $cachedStats['acknowledged'];
        $this->inProgress = $cachedStats['inProgress'];
        $this->escalated = $cachedStats['escalated'];
        $this->resolved = $cachedStats['resolved'];
        $this->unresolved = $cachedStats['unresolved'];
        $this->closed = $cachedStats['closed'];
        $this->overdue = $cachedStats['overdue'];
        $this->totalReceived = $cachedStats['totalReceived'];
        $this->latestGrievanceTicketId = $cachedStats['latestGrievanceTicketId'];
        $this->citizenCount = $cachedStats['citizenCount'];
        $this->activeFellowHrLiaisons = $cachedStats['activeFellowHrLiaisons'];
    }

}
