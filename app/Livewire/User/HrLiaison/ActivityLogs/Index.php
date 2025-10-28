<?php

namespace App\Livewire\User\HrLiaison\ActivityLogs;

use App\Models\ActivityLog;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Activity Logs')]
class Index extends Component
{
    use WithPagination;

    public int $limit = 10;
    public ?string $filter = null;

    protected $queryString = ['filter'];

    public function applyFilter(): void
    {
        $this->resetPage();
    }

    public function loadMore(): void
    {
        $this->limit += 10;
    }

    public function render()
    {
        $user = Auth::user();

        $departmentIds = DB::table('hr_liaison_departments')
            ->where('hr_liaison_id', $user->id)
            ->pluck('department_id')
            ->toArray();

        $hrLiaisonIds = DB::table('hr_liaison_departments')
            ->whereIn('department_id', $departmentIds)
            ->pluck('hr_liaison_id')
            ->unique()
            ->toArray();

        $grievanceIds = Assignment::whereIn('hr_liaison_id', $hrLiaisonIds)
            ->pluck('grievance_id')
            ->unique()
            ->toArray();

        $citizenIds = DB::table('grievances')
            ->whereIn('grievance_id', $grievanceIds)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $userIds = array_unique(array_merge($hrLiaisonIds, $citizenIds));

        $query = ActivityLog::query()
            ->whereIn('user_id', $userIds)
            ->when($this->filter, fn($q) => $q->where('module', $this->filter))
            ->latest('timestamp');

        $logs = $query->paginate($this->limit);

        $pageItems = collect($logs->items());

        $groupedLogs = $pageItems->groupBy(function ($log) {
            $date = Carbon::parse($log->timestamp)->startOfDay();
            $today = Carbon::now()->startOfDay();
            $yesterday = Carbon::now()->subDay()->startOfDay();

            if ($date->equalTo($today)) {
                return 'Today';
            }

            if ($date->equalTo($yesterday)) {
                return 'Yesterday';
            }

            return $date->format('F j, Y');
        });

        return view('livewire.user.hr-liaison.activity-logs.index', [
            'logs' => $logs,
            'groupedLogs' => $groupedLogs,
            'hasMore' => $logs->hasMorePages(),
        ]);
    }
}
