<?php

namespace App\Livewire\User\Admin\ReportsAndAnalytics;

use Livewire\Component;
use App\Models\Grievance;
use App\Models\Feedback;
use App\Models\User;
use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Reports & Analytics')]
class Index extends Component
{

    public $filterType = '';
    public $grievanceType;
    public $grievancePriority;
    public $grievanceStatus;
    public $dynamicGrievanceFilter;
    public $dynamicGrievanceOptions = [];
    public $filterGender;
    public $filterRegion;
    public $filterService;
    public $filterCCSummary;
    public $filterSQDSummary;
    public $filterUserType = 'Citizen';
    public $startDate;
    public $endDate;
    public $serviceOptions = [];

    public function mount()
    {
        $this->serviceOptions = Feedback::select('service')
            ->distinct()
            ->pluck('service')
            ->toArray();

        $this->dynamicGrievanceOptions = [
            'High → Low Priority' => 'high_low_priority',
            'Department with Most Grievances' => 'top_department',
            'Most Submitted Grievance Type' => 'top_grievance_type',
        ];
    }

    public function applyFilters()
    {

    }
    public function render()
    {
        $data = collect();

        switch ($this->filterType) {
            case 'Grievances':
                $data = Grievance::with(['user', 'departments'])
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))

                    ->when($this->grievanceType, fn($q) => $q->where('grievance_type', $this->grievanceType))
                    ->when($this->grievancePriority, fn($q) => $q->where('priority_level', $this->grievancePriority))
                    ->when($this->grievanceStatus, fn($q) => $q->where('grievance_status', $this->grievanceStatus))

                    ->when($this->dynamicGrievanceFilter === 'high_low_priority', fn($q) =>
                        $q->orderByRaw("FIELD(priority_level, 'High', 'Normal', 'Low')")
                    )

                    ->when($this->dynamicGrievanceFilter === 'top_department', function($q) {
                        $q->withCount('departments')->orderByDesc('departments_count');
                    })

                    ->when($this->dynamicGrievanceFilter === 'top_grievance_type', function($q) {
                        $q->leftJoin(
                            \DB::raw('(SELECT grievance_type, COUNT(*) AS type_count
                                    FROM grievances
                                    WHERE deleted_at IS NULL
                                    GROUP BY grievance_type) AS gcount'),
                            'grievances.grievance_type',
                            '=',
                            'gcount.grievance_type'
                        )
                        ->orderByDesc('gcount.type_count')
                        ->orderByDesc('grievances.created_at')
                        ->select('grievances.*');
                    })

                    ->when(!$this->dynamicGrievanceFilter, fn($q) => $q->latest())

                    ->get();
                break;


            case 'Feedbacks':
                $data = Feedback::with('user')
                    ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))

                    ->when($this->filterGender, fn($q) => $q->where('gender', $this->filterGender))
                    ->when($this->filterRegion, fn($q) => $q->where('region', $this->filterRegion))
                    ->when($this->filterService, fn($q) => $q->where('service', $this->filterService))
                    ->when($this->filterCCSummary, fn($q) => $q->where('cc_summary', $this->filterCCSummary))
                    ->when($this->filterSQDSummary, fn($q) => $q->where('sqd_summary', $this->filterSQDSummary))

                    ->latest()
                    ->get();
                break;


            case 'Users':
                $data = User::with(['roles', 'userInfo'])
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                    ->when($this->filterUserType === 'Citizen', function ($q) {
                        $q->whereHas('roles', fn($r) => $r->where('name', 'citizen'));
                    })
                    ->when($this->filterUserType === 'HR Liaison', function ($q) {
                        $q->whereHas('roles', fn($r) => $r->where('name', 'hr_liaison'));
                    })
                    ->latest()
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'roles' => $user->roles->pluck('name')->join(', '),
                            'status' => $user->status,
                            'created_at' => $user->created_at,
                            'userInfo' => $user->roles->pluck('name')->contains('citizen')
                                ? $user->userInfo
                                : null,
                            'departments' => $user->roles->pluck('name')->contains('hr_liaison')
                                ? $user->departments->pluck('department_name')->join(', ')
                                : null,
                        ];
                    });
                break;

        }

        return view('livewire.user.admin.reports-and-analytics.index', [
            'data' => $data
        ]);
    }
}
