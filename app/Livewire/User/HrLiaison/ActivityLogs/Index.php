<?php

namespace App\Livewire\User\HrLiaison\ActivityLogs;

use App\Models\ActivityLog;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Activity Logs')]
class Index extends Component
{
    use WithPagination;

    public int $limit = 10;

    public function render()
    {
        $user = Auth::user();

        // Get HR Liaisons in the same department(s)
        $departmentIds = \DB::table('hr_liaison_departments')
            ->where('hr_liaison_id', $user->id)
            ->pluck('department_id')
            ->toArray();

        $hrLiaisonIds = \DB::table('hr_liaison_departments')
            ->whereIn('department_id', $departmentIds)
            ->pluck('hr_liaison_id')
            ->unique()
            ->toArray();

        // Get grievances assigned to these HR Liaisons
        $grievanceIds = Assignment::whereIn('hr_liaison_id', $hrLiaisonIds)
            ->pluck('grievance_id')
            ->unique()
            ->toArray();

        // Get citizens who submitted those grievances
        $citizenIds = \DB::table('grievances')
            ->whereIn('grievance_id', $grievanceIds)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        // Merge all relevant user IDs
        $userIds = array_unique(array_merge($hrLiaisonIds, $citizenIds));

        // Fetch activity logs
        $logs = ActivityLog::with('user')
            ->whereIn('user_id', $userIds)
            ->latest('timestamp')
            ->paginate($this->limit);

        return view('livewire.user.hr-liaison.activity-logs.index', [
            'logs' => $logs,
        ]);
    }
}
