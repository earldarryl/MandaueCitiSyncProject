<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\Department;
use App\Models\Grievance;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('Grievance Reports')]
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

        $this->categoryOptions = [
            'Business Permit and Licensing Office' => [
                'Complaint' => [
                    'Delayed Business Permit Processing',
                    'Unclear Requirements or Procedures',
                    'Unfair Treatment by Personnel',
                ],
                'Inquiry' => [
                    'Business Permit Requirements Inquiry',
                    'Renewal Process Clarification',
                    'Schedule or Fee Inquiry',
                ],
                'Request' => [
                    'Document Correction or Update Request',
                    'Business Record Verification Request',
                    'Appointment or Processing Schedule Request',
                ],
            ],
            'Traffic Enforcement Agency of Mandaue' => [
                'Complaint' => [
                    'Traffic Enforcer Misconduct',
                    'Unjust Ticketing or Penalty',
                    'Inefficient Traffic Management',
                ],
                'Inquiry' => [
                    'Traffic Rules Clarification',
                    'Citation or Violation Inquiry',
                    'Inquiry About Traffic Assistance',
                ],
                'Request' => [
                    'Request for Traffic Assistance',
                    'Request for Event Traffic Coordination',
                    'Request for Violation Review',
                ],
            ],
            'City Social Welfare Services' => [
                'Complaint' => [
                    'Discrimination or Neglect in Assistance',
                    'Delayed Social Service Response',
                    'Unprofessional Staff Behavior',
                ],
                'Inquiry' => [
                    'Assistance Program Inquiry',
                    'Eligibility or Requirements Clarification',
                    'Social Service Schedule Inquiry',
                ],
                'Request' => [
                    'Request for Social Assistance',
                    'Financial Aid or Program Enrollment Request',
                    'Home Visit or Consultation Request',
                ],
            ],
        ];

        $flattened = [];
        foreach ($this->categoryOptions as $department => $types) {
            foreach ($types as $type => $categories) {
                foreach ($categories as $category) {
                    $flattened[$category] = $category;
                }
            }
        }

        $this->categoryOptions = $flattened;
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
                ->body('Grievance not found or already deleted.')
                ->danger()
                ->send();
            return;
        }

        $pdf = Pdf::loadView('pdf.grievance', [
            'grievance' => $grievance,
        ])->setPaper('A4', 'portrait');

        $filename = 'grievance-' . $grievance->grievance_id . '.pdf';

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
            'Content-Disposition' => 'attachment; filename="grievance_' . $grievance->grievance_id . '.csv"',
        ];

        $callback = function () use ($grievance) {
            $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    'Grievance Ticket ID',
                    'Grievance Title',
                    'Grievance Type',
                    'Grievance Category',
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
            Notification::make()
                ->title('Error')
                ->body('Grievance not found or already deleted.')
                ->danger()
                ->send();
            return;
        }

        $title = $grievance->grievance_title;
        $grievance->delete();

        Notification::make()
            ->title('Grievance Deleted')
            ->body("{$title} was removed successfully.")
            ->success()
            ->send();

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
        $this->selectAll = false;
        $this->selected = [];
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
            ->body('Selected grievances were removed successfully.')
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
            ->body('Selected grievances were marked as High Priority.')
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
            switch ($this->filterDate) {
                case 'Today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'Yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'This Week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'This Month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'This Year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
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
            ->when($this->filterDate, function($q){
                switch($this->filterDate){
                    case 'Today':
                        $q->whereDate('created_at', now()->toDateString());
                        break;
                    case 'Yesterday':
                        $q->whereDate('created_at', now()->subDay()->toDateString());
                        break;
                    case 'This Week':
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'This Month':
                        $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                        break;
                    case 'This Year':
                        $q->whereYear('created_at', now()->year);
                        break;
                }
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
