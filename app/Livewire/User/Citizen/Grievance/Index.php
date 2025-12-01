<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Grievance;
use App\Models\EditRequest;
use App\Models\User;
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
            Notification::make()
                ->title('Welcome back, ' . $this->user->name)
                ->body('Good to see you again! Here’s your dashboard.')
                ->success()
                ->send();
        }

        if (session()->has('notification')) {
            $notif = session('notification');
            Notification::make()
                ->title($notif['title'])
                ->body($notif['body'])
                ->{$notif['type']}()
                ->send();
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
            Notification::make()
                ->title('Request Not Allowed')
                ->body('You already have submitted an edit request for this report.')
                ->warning()
                ->send();
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

        Notification::make()
            ->title('Request Sent')
            ->body('Your edit request has been sent to the assigned HR Liaisons and Admin.')
            ->success()
            ->send();

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

    public function downloadPdf($id)
    {
        $grievance = Grievance::with(['departments', 'attachments', 'user'])->find($id);

        if (! $grievance) {
            Notification::make()
                ->title('Error')
                ->body('Report not found or already deleted.')
                ->danger()
                ->send();
            return;
        }

        $pdf = Pdf::loadView('pdf.grievance', [
            'grievance' => $grievance,
        ])->setPaper('A4', 'portrait');

        $filename = 'report-' . $grievance->grievance_id . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    public function downloadCsv($id)
    {
        $grievance = Grievance::with(['user.info', 'departments'])->findOrFail($id);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report_' . $grievance->grievance_id . '.csv"',
        ];

        $callback = function () use ($grievance) {
            $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    'Report Ticket ID',
                    'Report Title',
                    'Report Type',
                    'Report Category',
                    'Priority Level',
                    'Status',
                    'Submitted By',
                    'Departments Involved',
                    'Details',
                    'Created At',
                    'Updated At',
                ]);

            $submittedBy = $grievance->is_anonymous
                ? 'Anonymous'
                : ($grievance->user?->info
                    ? "{$grievance->user->info->first_name} {$grievance->user->info->last_name}"
                    : $grievance->user?->name);

            $departments = $grievance->departments->pluck('department_name')->join(', ') ?: 'N/A';

            fputcsv($handle, [
                $grievance->grievance_ticket_id,
                $grievance->grievance_title,
                $grievance->grievance_type,
                $grievance->grievance_category,
                $grievance->priority_level,
                ucfirst(str_replace('_', ' ', $grievance->grievance_status)),
                $submittedBy,
                $departments,
                strip_tags($grievance->grievance_details),
                $grievance->formatted_created_at,
                $grievance->formatted_updated_at,
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
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

    public function deleteGrievance($grievanceId)
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

        $grievance->delete();

        auth()->user()->notify(new GeneralNotification(
            'Report Deleted',
            "{$title} was removed successfully.",
            'warning',
            ['grievance_id' => $grievanceId],
            [],
            true,
            [
                [
                    'label'    => 'Undo Delete',
                    'color'    => 'gray',
                    'dispatch' => 'undoLatestGrievance',
                    'wireClick' => "undoGrievance('{$grievance->grievance_id}')",
                    'close'    => true,
                ]
            ]
        ));
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

    public function deleteSelected()
    {
        if (empty($this->selected)) return;

        $grievances = Grievance::whereIn('grievance_id', $this->selected)->get();

        foreach ($grievances as $grievance) {
            $grievance->delete();
        }

        $this->selected = [];
        $this->selectAll = false;
        $this->updateStats();

        Notification::make()
            ->title('Bulk Remove')
            ->body('Selected reports were removed successfully.')
            ->success()
            ->send();
    }

    public function markSelectedHighPriority()
    {
        if (empty($this->selected)) return;

        Grievance::whereIn('grievance_id', $this->selected)
            ->update(['priority_level' => 'High']);

        $this->selected = [];
        $this->selectAll = false;
        $this->updateStats();

        Notification::make()
            ->title('Bulk Update')
            ->body('Selected reports were marked as High Priority.')
            ->success()
            ->send();
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
