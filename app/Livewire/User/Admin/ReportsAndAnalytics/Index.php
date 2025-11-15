<?php

namespace App\Livewire\User\Admin\ReportsAndAnalytics;

use App\Models\Department;
use Livewire\Component;
use App\Models\Grievance;
use App\Models\Feedback;
use App\Models\User;
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
    public $dynamicGrievanceStats = [];
    public $dynamicGrievanceOptions;
    public $dynamicDepartmentFilter;
    public $dynamicDepartmentStats = [];
    public $dynamicDepartmentFilterOptions;
    public $filterServiceStatus;
    public $filterServiceAvailability;
    public $filterGender;
    public $filterRegion;
    public $filterService;
    public $filterCCSummary;
    public $filterSQDSummary;
    public $filterUserType = 'Citizen';
    public $startDate;
    public $endDate;
    public $serviceOptions = [];
    public $colorMap = [
        'Pending'      => ['bg' => 'from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800', 'text' => 'text-yellow-600 dark:text-yellow-400'],
        'Acknowledged' => ['bg' => 'from-teal-50 to-teal-100 dark:from-teal-900 dark:to-teal-800', 'text' => 'text-teal-600 dark:text-teal-400'],
        'In Progress'  => ['bg' => 'from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800', 'text' => 'text-blue-600 dark:text-blue-400'],
        'Escalated'    => ['bg' => 'from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800', 'text' => 'text-orange-600 dark:text-orange-400'],
        'Resolved'     => ['bg' => 'from-green-50 to-green-100 dark:from-green-900 dark:to-green-800', 'text' => 'text-green-600 dark:text-green-400'],
        'Unresolved'   => ['bg' => 'from-red-50 to-red-100 dark:from-red-900 dark:to-red-800', 'text' => 'text-red-600 dark:text-red-400'],
        'Closed'       => ['bg' => 'from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800', 'text' => 'text-gray-600 dark:text-gray-400'],
        'Delayed'      => ['bg' => 'from-yellow-100 to-yellow-200 dark:from-yellow-800 dark:to-yellow-700', 'text' => 'text-yellow-800 dark:text-yellow-300'],

        'High'   => ['bg' => 'from-red-50 to-red-100 dark:from-red-900 dark:to-red-800', 'text' => 'text-red-600 dark:text-red-400'],
        'Normal' => ['bg' => 'from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800', 'text' => 'text-yellow-600 dark:text-yellow-400'],
        'Low'    => ['bg' => 'from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800', 'text' => 'text-blue-600 dark:text-blue-400'],

        'Complaint' => ['bg' => 'from-red-50 to-red-100 dark:from-red-900 dark:to-red-800', 'text' => 'text-red-600 dark:text-red-400'],
        'Request'   => ['bg' => 'from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800', 'text' => 'text-blue-600 dark:text-blue-400'],
        'Inquiry'   => ['bg' => 'from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800', 'text' => 'text-purple-600 dark:text-purple-400'],
    ];


    public function mount()
    {
        $this->serviceOptions = Feedback::select('service')
            ->distinct()
            ->pluck('service')
            ->toArray();

        $this->dynamicGrievanceOptions = [
            'High → Low Priority' => 'High → Low Priority',
            'Most Submitted Grievance Type' => 'Most Submitted Grievance Type',
            'Status Counts' => 'Status Counts',
        ];

        $this->dynamicDepartmentFilterOptions = [
            'Most Assignments' => 'Most Assignments',
            'Most Active & Available' => 'Most Active & Available',
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
                    ->latest()
                    ->get();
                break;

            case 'Departments':
                $data = Department::withCount('assignments')
                    ->when($this->filterServiceStatus, fn($q) => $q->where('is_active', $this->filterServiceStatus === 'Active'))
                    ->when($this->filterServiceAvailability, fn($q) => $q->where('is_available', $this->filterServiceAvailability === 'Available'))
                    ->with('hrLiaisons')
                    ->latest()
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

        $this->dynamicGrievanceStats = [];

        if ($this->filterType === 'Grievances' && $this->dynamicGrievanceFilter) {

            $baseQuery = Grievance::query()
                ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));

            if ($this->dynamicGrievanceFilter === 'High → Low Priority') {
                $predefinedPriority = [
                    'High',
                    'Normal',
                    'Low',
                ];

                $raw = $baseQuery
                    ->selectRaw("priority_level, COUNT(*) as total")
                    ->groupBy('priority_level')
                    ->pluck('total', 'priority_level');

                $this->dynamicGrievanceStats = collect($predefinedPriority)->map(function ($priority) use ($raw) {
                    return (object)[
                        'priority_level' => $priority,
                        'total' => $raw[$priority] ?? 0
                    ];
                });

            }

            if ($this->dynamicGrievanceFilter === 'Most Submitted Grievance Type') {
                $predefinedTypes = [
                    'Complaint',
                    'Request',
                    'Inquiry'
                ];

                $raw = $baseQuery
                    ->selectRaw("grievance_type, COUNT(*) as total")
                    ->groupBy('grievance_type')
                    ->pluck('total', 'grievance_type');

                $this->dynamicGrievanceStats = collect($predefinedTypes)->map(function ($type) use ($raw) {
                    return (object)[
                        'grievance_type' => $type,
                        'total' => $raw[$type] ?? 0
                    ];
                });

            }

            if ($this->dynamicGrievanceFilter === 'Status Counts') {

                $statuses = [
                    'Pending' => 0,
                    'Delayed' => 0,
                    'Acknowledged' => 0,
                    'In Progress' => 0,
                    'Escalated' => 0,
                    'Resolved' => 0,
                    'Unresolved' => 0,
                    'Closed' => 0,
                ];

                $grievances = $baseQuery->get();
                $now = now();

                foreach ($grievances as $grievance) {
                    $status = strtolower($grievance->grievance_status ?? '');
                    $daysPassed = $grievance->created_at ? $grievance->created_at->diffInDays($now) : 0;
                    $processingDays = $grievance->processing_days ?? 0;

                    if ($status === 'resolved') {
                        $statuses['Resolved']++;
                    } elseif ($status === 'pending' || $status === '') {
                        if ($processingDays > 0 && $daysPassed > $processingDays) {
                            $statuses['Delayed']++;
                        } else {
                            $statuses['Pending']++;
                        }
                    } elseif ($status === 'acknowledged') {
                        $statuses['Acknowledged']++;
                    } elseif ($status === 'in_progress') {
                        $statuses['In Progress']++;
                    } elseif ($status === 'escalated') {
                        $statuses['Escalated']++;
                    } elseif ($status === 'unresolved') {
                        $statuses['Unresolved']++;
                    } elseif ($status === 'closed') {
                        $statuses['Closed']++;
                    } else {
                        $statuses['Pending']++;
                    }
                }

                $this->dynamicGrievanceStats = collect($statuses)->map(function($total, $status) {
                    return (object)[
                        'grievance_status' => $status,
                        'total' => $total
                    ];
                });
            }


        }

        $this->dynamicDepartmentStats = [];

        if ($this->filterType === 'Departments' && $this->dynamicDepartmentFilter) {

            if ($this->dynamicDepartmentFilter === 'Most Assignments') {
                $this->dynamicDepartmentStats = $data
                    ->sortByDesc('assignments_count')
                    ->take(5)
                    ->map(function ($dept) {
                        return (object)[
                            'department_name' => $dept->department_name,
                            'total' => $dept->assignments_count,
                            'icon' => 'heroicon-o-briefcase',
                        ];
                    })->values();
            }

            if ($this->dynamicDepartmentFilter === 'Most Active & Available') {
                $this->dynamicDepartmentStats = $data
                    ->filter(fn($dept) => $dept->is_active && $dept->is_available)
                    ->sortByDesc(fn($dept) => $dept->hrLiaisons->filter(fn($user) => $user->isOnline())->count())
                    ->take(5)
                    ->map(function ($dept) {
                        $activeLiaisons = $dept->hrLiaisons->filter(fn($user) => $user->isOnline())->count();
                        $totalLiaisons = $dept->hrLiaisons->count();
                        return (object)[
                            'department_name' => $dept->department_name,
                            'total' => "$activeLiaisons / $totalLiaisons",
                            'icon' => 'heroicon-o-users',
                        ];
                    })->values();
            }

        }

        return view('livewire.user.admin.reports-and-analytics.index', [
            'data' => $data,
            'stats' => $this->filterType === 'Departments' ? $this->dynamicDepartmentStats : $this->dynamicGrievanceStats,
        ]);
    }
}
