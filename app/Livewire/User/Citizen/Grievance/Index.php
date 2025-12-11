<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Grievance;
use App\Models\EditRequest;
use App\Models\User;
use App\Models\HistoryLog;
use Illuminate\Support\Facades\Request;
use App\Notifications\GeneralNotification;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('My Reports')]
class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public ?string $sortField = null;
    public string $sortDirection = 'asc';
    public $user;
    public $perPage = 10;
    public $search = '';
    public $searchInput = '';
    public $filterPriority = '';
    public $filterStatus = '';
    public $filterType = '';
    public $filterDepartment = '';
    public $filterCategory = '';
    public $filterDate = '';
    public $filterEditable = '';
    public $selected = [];
    public $selectAll = false;
    public $totalGrievances = 0;
    public $highPriorityCount = 0;
    public $normalPriorityCount = 0;
    public $lowPriorityCount = 0;
    public $pendingCount = 0;
    public $inProgressCount = 0;
    public $resolvedCount = 0;
    public $unresolvedCount = 0;
    public $closedCount = 0;
    public $overdueCount = 0;
    public $departmentOptions;
    public $categoryOptions;
    public bool $showFeedbackModal = false;
    public bool $dontShowAgain = false;

    protected $updatesQueryString = [
        'search' => ['except' => ''],
        'filterPriority' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterDate' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterDepartment' => ['except' => ''],
        'filterEditable' => ['except' => ''],
        'page'           => ['except' => 1],
    ];

    protected $listeners = [
        'poll' => '$refresh',
    ];

   public function mount()
    {
        $this->user = auth()->user();

        if (session()->pull('just_logged_in', false)) {

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Welcome back, ' . $this->user->name,
                'message' => "Good to see you again! Here’s your dashboard.",
            ]);
        }

        if (session()->has('notification')) {
            $notif = session('notification');
            $this->dispatch('notify', [
                'type' => $notif['type'],
                'title' => $notif['title'],
                'message' => $notif['body'],
            ]);
        }

        if (session()->get('hide_feedback_modal', false)) {
            return;
        }

        if (session()->pull('grievance_submitted_once', false)) {
            $this->showFeedbackModal = true;
        }

        $this->updateStats();

        $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->pluck('department_name', 'department_name')
            ->toArray();

        $grievanceCategories = Grievance::distinct()
            ->pluck('grievance_category')
            ->filter()
            ->toArray();

        $this->categoryOptions = [];
        foreach ($grievanceCategories as $category) {
            $this->categoryOptions[$category] = $category;
        }

    }

    public function requestEditPermission($grievanceId)
    {
        $grievance = Grievance::findOrFail($grievanceId);
        $user = auth()->user();

        $existing = EditRequest::where('grievance_id', $grievance->grievance_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'Request Not Allowed',
                'message' => "You already have submitted an edit request for this report.",
            ]);
            return;
        }


        $editRequest = EditRequest::create([
            'grievance_id' => $grievance->grievance_id,
            'user_id'      => $user->id,
            'status'       => 'pending',
            'reason'       => 'User requested edit permission.',
        ]);

        $requesterName = $grievance->is_anonymous ? 'An anonymous user' : $user->name;

        $assignedHrIds = $grievance->assignments->pluck('hr_liaison_id')->unique();
        $hrLiaisons = User::whereIn('id', $assignedHrIds)->get();

        foreach ($hrLiaisons as $hr) {
            $hr->notify(new GeneralNotification(
                'Edit Request Pending',
                "{$requesterName} requested permission to edit report '{$grievance->grievance_title}'.",
                'info',
                [
                    'grievance_ticket_id' => $grievance->grievance_ticket_id,
                    'edit_request_id'     => $editRequest->id
                ],
                [],
                true,
                [
                    [
                        'label' => 'Review Request',
                        'url'   => route('hr-liaison.grievance.view', $grievance->grievance_ticket_id),
                        'open_new_tab' => true,
                    ],
                    [
                        'label' => 'Approve',
                        'action' => 'approveEditRequest',
                        'color'  => 'green',
                        'icon'   => 'heroicon-o-check',
                    ],
                    [
                        'label' => 'Deny',
                        'action' => 'denyEditRequest',
                        'color'  => 'red',
                        'icon'   => 'heroicon-o-x-mark',
                    ]
                ]
            ));
        }

        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Edit Request Submitted',
                "{$requesterName} requested permission to edit report '{$grievance->grievance_title}'.",
                'warning',
                [
                    'grievance_ticket_id' => $grievance->grievance_ticket_id,
                    'edit_request_id' => $editRequest->id
                ],
                [],
                true,
                [
                    [
                        'label' => 'Review Request',
                        'url'   => route('admin.forms.grievances.view', $grievance->grievance_ticket_id),
                        'open_new_tab' => true,
                    ]
                ]
            ));
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Request Sent',
            'message' => "Your edit request has been sent to the assigned HR Liaisons and Admin.",
        ]);

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Report Edit Request',
            'action'       => 'create',
            'action_type'  => 'request_edit',
            'model_type'   => EditRequest::class,
            'model_id'     => $editRequest->id,
            'description'  => "{$requesterName} requested edit permission for report '{$grievance->grievance_ticket_id}'.",
            'changes'      => null,
            'status'      => 'success',
            'ip_address'  => request()->ip(),
            'device_info' => request()->header('device') ?? null,
            'user_agent'  => request()->userAgent(),
            'platform'    => php_uname('s'),
            'location'    => null,
            'timestamp'   => now(),
        ]);
    }

    public function closeFeedbackModal()
    {
        if ($this->dontShowAgain) {
            session()->put('hide_feedback_modal', true);
        }

        $this->showFeedbackModal = false;
    }
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function applySearch()
    {
        $this->search = $this->searchInput;
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
        $this->updateStats();
    }

    public function clearSearch()
    {
        $this->searchInput = '';
        $this->search = '';
        $this->resetPage();
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) return;

        $grievances = Grievance::whereIn('grievance_id', $this->selected)->get();

        foreach ($grievances as $grievance) {

            $department = $grievance->department;

            $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                ->whereHas('departments', fn($q) =>
                    $q->where('hr_liaison_departments.department_id', $department->department_id)
                )->get();

            foreach ($hrLiaisons as $hr) {
                $hr->notify(new GeneralNotification(
                    'Report Deleted',
                    "A report titled '{$grievance->grievance_title}' has been deleted.",
                    'danger',
                    ['grievance_ticket_id' => $grievance->grievance_ticket_id]
                ));
            }

            $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
            foreach ($admins as $admin) {
                $admin->notify(new GeneralNotification(
                    'Report Deleted',
                    "A report titled '{$grievance->grievance_title}' has been deleted from the system.",
                    'warning',
                    ['grievance_ticket_id' => $grievance->grievance_ticket_id]
                ));
            }

            ActivityLog::create([
                'user_id'      => auth()->id(),
                'role_id'      => auth()->user()->roles->first()->id ?? null,
                'module'       => 'Reports',
                'action'       => 'delete',
                'action_type'  => 'bulk_delete',
                'model_type'   => Grievance::class,
                'model_id'     => $grievance->grievance_id,
                'description'  => "Grievance '{$grievance->grievance_title}' deleted by user.",
                'changes'      => null,
                'status'       => 'success',
                'ip_address'   => Request::ip(),
                'device_info'  => request()->header('User-Agent'),
                'user_agent'   => request()->userAgent(),
                'platform'     => php_uname(),
                'location'     => null,
                'timestamp'    => now(),
            ]);

            HistoryLog::create([
                'user_id'        => auth()->id(),
                'action_type'    => 'delete',
                'description'    => "Deleted report '{$grievance->grievance_title}'",
                'reference_table'=> 'grievances',
                'reference_id'   => $grievance->grievance_id,
                'ip_address'     => Request::ip(),
            ]);

            $grievance->delete();
        }

        $this->selected = [];
        $this->selectAll = false;
        $this->updateStats();
        $this->dispatch('close-modal-delete');

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Bulk Remove',
            'message' => "Selected reports were removed successfully.",
        ]);
    }

    public function deleteReport($grievanceId)
    {
        $grievance = Grievance::find($grievanceId);

        if (! $grievance) {
            auth()->user()->notify(new GeneralNotification(
                'Error',
                'Report not found or already deleted.',
                'danger'
            ));
            return;
        }

        $title = $grievance->grievance_title;

        $department = $grievance->department;

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'role_id'      => auth()->user()->roles->first()->id ?? null,
            'module'       => 'Reports',
            'action'       => 'delete',
            'action_type'  => 'single_delete',
            'model_type'   => Grievance::class,
            'model_id'     => $grievance->grievance_id,
            'description'  => "Report '{$title}' deleted by user.",
            'changes'      => null,
            'status'       => 'success',
            'ip_address'   => Request::ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => request()->userAgent(),
            'platform'     => php_uname(),
            'location'     => null,
            'timestamp'    => now(),
        ]);

        HistoryLog::create([
            'user_id'        => auth()->id(),
            'action_type'    => 'delete',
            'description'    => "Deleted report '{$title}'",
            'reference_table'=> 'grievances',
            'reference_id'   => $grievance->grievance_id,
            'ip_address'     => Request::ip(),
        ]);

        $grievance->delete();

        $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) =>
                $q->where('hr_liaison_departments.department_id', $department->department_id)
            )->get();

        foreach ($hrLiaisons as $hr) {
            $hr->notify(new GeneralNotification(
                'Report Deleted',
                "A report titled '{$title}' has been deleted.",
                'danger',
                ['grievance_ticket_id' => $grievance->grievance_ticket_id]
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Report Deleted',
                "A grievance titled '{$title}' has been deleted from the system.",
                'warning',
                ['grievance_ticket_id' => $grievance->grievance_ticket_id]
            ));
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Report Removed',
            'message' => "Report {$title} has been deleted successfully.",
            'grievance_ticket_id' => $grievance->grievance_ticket_id,
        ]);
    }

    private function sendMessage($message, $type = 'info')
    {
        auth()->user()->notify(new GeneralNotification(
            ucfirst($type),
            $message,
            $type
        ));
    }

    #[On('undoLatestGrievance')]
    public function undoLatestGrievance()
    {
        $grievance = Grievance::onlyTrashed()
            ->latest('deleted_at')
            ->first();

        if (! $grievance) {
            $this->sendMessage('Nothing to undo.', 'danger');
            return;
        }

        $grievance->restore();

        $this->sendMessage("{$grievance->grievance_title} restored successfully.", 'success');

        $this->updateStats();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $grievances = Grievance::where('user_id', auth()->id())->latest()->get();
            $this->selected = $grievances->pluck('grievance_id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSearch()
    {
        $this->resetSelection();
    }

    public function updatedFilters()
    {
        $this->resetSelection();
    }

    public function updatingPage()
    {
        $this->resetSelection();
    }

    protected function resetSelection()
    {
        // $this->selectAll = false;
        // $this->selected = [];
    }

    public function updateStats()
    {
        $query = Grievance::where('user_id', auth()->id());

        if ($this->filterPriority) {
            $query->where('priority_level', $this->filterPriority);
        }

        if ($this->filterStatus) {
            $map = [
                'Pending' => 'pending',
                'Acknowledged' => 'acknowledged',
                'In Progress' => 'in_progress',
                'Escalated' => 'escalated',
                'Resolved' => 'resolved',
                'Unresolved' => 'unresolved',
                'Closed' => 'closed',
                'Overdue' => 'overdue'
            ];
            $query->when(isset($map[$this->filterStatus]), fn($q) => $q->where('grievance_status', $map[$this->filterStatus]));
        }

        if ($this->filterType) {
            $query->where('grievance_type', $this->filterType);
        }

        if ($this->filterCategory) {
            $query->where('grievance_category', $this->filterCategory);
        }

        if ($this->filterDepartment) {
            $query->whereHas('departments', function ($q) {
                $q->where('department_name', $this->filterDepartment);
            });
        }

        if ($this->filterDate) {
            $query->whereDate('created_at', $this->filterDate);
        }

        $this->totalGrievances     = $query->count();
        $this->highPriorityCount   = (clone $query)->where('priority_level', 'High')->count();
        $this->normalPriorityCount = (clone $query)->where('priority_level', 'Normal')->count();
        $this->lowPriorityCount    = (clone $query)->where('priority_level', 'Low')->count();

        $this->pendingCount      = (clone $query)->where('grievance_status', 'pending')->count();
        $this->acknowledgedCount = (clone $query)->where('grievance_status', 'acknowledged')->count();
        $this->inProgressCount   = (clone $query)->where('grievance_status', 'in_progress')->count();
        $this->escalatedCount    = (clone $query)->where('grievance_status', 'escalated')->count();
        $this->resolvedCount     = (clone $query)->where('grievance_status', 'resolved')->count();
        $this->unresolvedCount     = (clone $query)->where('grievance_status', 'unresolved')->count();
        $this->closedCount       = (clone $query)->where('grievance_status', 'closed')->count();
        $this->overdueCount       = (clone $query)->where('grievance_status', 'overdue')->count();
    }

    public function render()
    {
        $grievances = Grievance::with(['departments' => fn($q) => $q->distinct(), 'attachments', 'user'])
            ->where('user_id', auth()->id())
            ->when($this->filterPriority, fn($q) => $q->where('priority_level', $this->filterPriority))
            ->when($this->filterStatus, function($q) {
                $map = [
                    'Pending' => 'pending',
                    'Acknowledged' => 'acknowledged',
                    'In Progress' => 'in_progress',
                    'Escalated' => 'escalated',
                    'Resolved' => 'resolved',
                    'Unresolved' => 'unresolved',
                    'Closed' => 'closed',
                    'Overdue' => 'overdue'
                ];
                if(isset($map[$this->filterStatus])) $q->where('grievance_status', $map[$this->filterStatus]);
            })
            ->when($this->filterType, fn($q) => $q->where('grievance_type', $this->filterType))
            ->when($this->filterCategory, fn($q) => $q->where('grievance_category', $this->filterCategory))
            ->when($this->filterDepartment, function ($q) {
                $q->whereHas('departments', function ($sub) {
                    $sub->where('department_name', $this->filterDepartment);
                });
            })
            ->when($this->filterEditable, function($query) {
                $userId = auth()->id();

                if ($this->filterEditable === 'Editable') {
                    $query->whereHas('editRequests', function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                        ->where('status', 'approved');
                    });
                } elseif ($this->filterEditable === 'Not Editable') {
                    $query->whereDoesntHave('editRequests', function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                        ->where('status', 'approved');
                    });
                }
            })
            ->when($this->search, function ($query) {
                $term = trim($this->search);

                $normalized = str_replace([' ', '-'], '_', strtolower($term));

                $query->where(function ($sub) use ($term, $normalized) {
                    $sub->where('grievance_title', 'like', "%{$term}%")
                        ->orWhere('grievance_details', 'like', "%{$term}%")
                        ->orWhere('priority_level', 'like', "%{$term}%")
                        ->orWhere('grievance_type', 'like', "%{$term}%")
                        ->orWhere('grievance_category', 'like', "%{$term}%")
                        ->orWhere('is_anonymous', 'like', "%{$term}%")
                        ->orWhereRaw('CAST(grievance_ticket_id AS CHAR) like ?', ["%{$term}%"])
                        ->orWhere('grievance_status', 'like', "%{$term}%")
                        ->orWhere('grievance_status', 'like', "%{$normalized}%")
                        ->orWhereHas('departments', function ($dept) use ($term) {
                            $dept->where('department_name', 'like', "%{$term}%");
                        });
                });
            })
            ->when($this->filterDate, function ($q) {
                $q->whereDate('created_at', $this->filterDate);
            })
            ->when($this->sortField === 'departments.department_name', function ($q) {
                $q->select('grievances.*')
                ->leftJoin('department_grievance', 'grievances.grievance_id', '=', 'department_grievance.grievance_id')
                ->leftJoin('departments', 'department_grievance.department_id', '=', 'departments.department_id')
                ->orderBy('departments.department_name', $this->sortDirection)
                ->distinct();
            }, function($q) {
                $q->orderBy($this->sortField ?? 'created_at', $this->sortDirection ?? 'desc');
            })
            ->when($this->sortField, function($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            }, function($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($this->perPage);

        return view('livewire.user.citizen.grievance.index', [
            'grievances' => $grievances,
        ]);
    }
}
