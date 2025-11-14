<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\Grievance;
use App\Models\User;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
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
    public $perPage = 10;
    public $search = '';
    public $searchInput = '';
    public $filterPriority = '';
    public $filterStatus = 'Pending';
    public $filterType = '';
    public $filterCategory = '';
    public $filterDate = '';
    public $filterIdentity = '';
    public $selectAll = false;
    public $selected = [];
    public $department;
    public $category;
    public $status;
    public $departmentOptions;
    public $categoryOptions;
    public $totalGrievances = 0;
    public $highPriorityCount = 0;
    public $normalPriorityCount = 0;
    public $lowPriorityCount = 0;
    public $pendingCount;
    public $acknowledgedCount;
    public $inProgressCount;
    public $escalatedCount;
    public $resolvedCount;
    public $unresolvedCount;
    public $closedCount;


    protected $updatesQueryString = [
        'search' => ['except' => ''],
        'filterPriority' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterDate' => ['except' => ''],
        'filterCategory' => ['except' => ''],
    ];

    protected $listeners = [
        'poll' => '$refresh',
    ];

    public function mount()
    {
        $this->updateStats();

        if (session()->has('notification')) {
            $notif = session('notification');
            Notification::make()
                ->title($notif['title'])
                ->body($notif['body'])
                ->{$notif['type']}()
                ->send();
        }

        $currentHrLiaison = auth()->user();

        $excludedDepartmentIds = $currentHrLiaison->departments->pluck('department_id');

        $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->whereNotIn('department_id', $excludedDepartmentIds)
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

    public function updatedSelectAll($value)
    {
        $user = auth()->user();

        $query = Grievance::whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->when($this->filterPriority, fn($q) => $q->where('priority_level', $this->filterPriority))
            ->when($this->filterStatus, function ($q) {
                $map = [
                    'Pending' => 'pending',
                    'Acknowledged' => 'acknowledged',
                    'In Progress' => 'in_progress',
                    'Escalated' => 'escalated',
                    'Resolved' => 'resolved',
                    'Unresolved' => 'unresolved',
                    'Closed' => 'closed',
                ];
                if (isset($map[$this->filterStatus])) {
                    $q->where('grievance_status', $map[$this->filterStatus]);
                }
            })
            ->when($this->filterType, fn($q) => $q->where('grievance_type', $this->filterType))
            ->when($this->search, function ($query) {
                $term = trim($this->search);
                $normalized = str_replace([' ', '-'], '_', strtolower($term));
                $query->where(function ($sub) use ($term, $normalized) {
                    $sub->where('grievance_title', 'like', "%{$term}%")
                        ->orWhere('grievance_details', 'like', "%{$term}%")
                        ->orWhere('priority_level', 'like', "%{$term}%")
                        ->orWhere('grievance_type', 'like', "%{$term}%")
                        ->orWhere('is_anonymous', 'like', "%{$term}%")
                        ->orWhereRaw('CAST(grievance_id AS CHAR) like ?', ["%{$term}%"])
                        ->orWhere('grievance_status', 'like', "%{$term}%")
                        ->orWhere('grievance_status', 'like', "%{$normalized}%");
                });
            });

        $this->selected = $value
            ? $query->pluck('grievance_id')->toArray()
            : [];
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

    // public function exportSelectedGrievancesExcel()
    // {
    //     if (empty($this->selected)) {
    //         Notification::make()
    //             ->title('No Grievances Selected')
    //             ->body('Please select at least one grievance to export.')
    //             ->warning()
    //             ->send();
    //         return;
    //     }

    //     $user = auth()->user();

    //     $grievances = Grievance::with(['user.info', 'departments'])
    //         ->whereIn('grievance_id', $this->selected)
    //         ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
    //         ->latest()
    //         ->get();

    //     if ($grievances->isEmpty()) {
    //         Notification::make()
    //             ->title('No Grievances Found')
    //             ->body('The selected grievances were not found or are not assigned to you.')
    //             ->warning()
    //             ->send();
    //         return;
    //     }

    //     $data = $grievances->map(function ($grievance) {
    //         $submittedBy = $grievance->is_anonymous
    //             ? 'Anonymous'
    //             : ($grievance->user?->info
    //                 ? "{$grievance->user->info->first_name} {$grievance->user->info->last_name}"
    //                 : $grievance->user?->name);

    //         $departments = $grievance->departments->pluck('department_name')->join(', ') ?: 'N/A';

    //         return [
    //             'Grievance ID' => $grievance->grievance_id,
    //             'Title' => $grievance->grievance_title,
    //             'Type' => $grievance->grievance_type,
    //             'Category' => $grievance->grievance_category,
    //             'Priority' => $grievance->priority_level,
    //             'Status' => ucfirst(str_replace('_', ' ', $grievance->grievance_status)),
    //             'Submitted By' => $submittedBy,
    //             'Departments' => $departments,
    //             'Details' => strip_tags($grievance->grievance_details),
    //             'Created At' => $grievance->created_at->format('Y-m-d H:i:s'),
    //             'Updated At' => $grievance->updated_at->format('Y-m-d H:i:s'),
    //         ];
    //     })->toArray();

    //     $filename = 'selected_grievances_' . now()->format('Y_m_d_His') . '.xlsx';

    //     return Excel::download(new \Maatwebsite\Excel\Collections\SheetCollection([
    //         'Grievances' => $data
    //     ]), $filename);
    // }

    // public function downloadGrievancesExcel()
    // {
    //     $user = auth()->user();

    //     $grievances = Grievance::with(['user.info', 'departments'])
    //         ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
    //         ->latest()
    //         ->get();

    //     if ($grievances->isEmpty()) {
    //         Notification::make()
    //             ->title('No Grievances Found')
    //             ->body('There are no grievances assigned to you to export.')
    //             ->warning()
    //             ->send();
    //         return;
    //     }

    //     $data = $grievances->map(function ($grievance) {
    //         $submittedBy = $grievance->is_anonymous
    //             ? 'Anonymous'
    //             : ($grievance->user?->info
    //                 ? "{$grievance->user->info->first_name} {$grievance->user->info->last_name}"
    //                 : $grievance->user?->name);

    //         $departments = $grievance->departments->pluck('department_name')->join(', ') ?: 'N/A';

    //         return [
    //             'Grievance Ticket ID' => $grievance->grievance_ticket_id,
    //             'Title' => $grievance->grievance_title,
    //             'Type' => $grievance->grievance_type,
    //             'Priority' => $grievance->priority_level,
    //             'Status' => ucfirst(str_replace('_', ' ', $grievance->grievance_status)),
    //             'Submitted By' => $submittedBy,
    //             'Departments' => $departments,
    //             'Details' => strip_tags($grievance->grievance_details),
    //             'Created At' => $grievance->created_at->format('Y-m-d H:i:s'),
    //             'Updated At' => $grievance->updated_at->format('Y-m-d H:i:s'),
    //         ];
    //     })->toArray();

    //     $filename = 'grievances_assigned_to_' . $user->id . '_' . now()->format('Y_m_d_His') . '.xlsx';

    //     return Excel::download(new \Maatwebsite\Excel\Collections\SheetCollection([
    //         'Grievances' => $data
    //     ]), $filename);
    // }

    public function printSelectedGrievances()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->title('No Grievances Selected')
                ->body('Please select at least one grievance to print.')
                ->warning()
                ->send();
            return;
        }

        $user = auth()->user();

        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->whereIn('grievance_id', $this->selected)
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->get();

        if ($grievances->isEmpty()) {
            Notification::make()
                ->title('No Grievances Found')
                ->body('The selected grievances were not found or are not assigned to you.')
                ->warning()
                ->send();
            return;
        }

        return redirect()->route('print-selected-grievances', [
            'selected' => implode(',', $this->selected),
        ]);
    }

    public function exportSelectedGrievancesCsv()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->title('No Grievances Selected')
                ->body('Please select at least one grievance to export.')
                ->warning()
                ->send();
            return;
        }

        $user = auth()->user();

        $grievances = Grievance::with(['user.info', 'departments'])
            ->whereIn('grievance_id', $this->selected)
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            Notification::make()
                ->title('No Grievances Found')
                ->body('The selected grievances were not found or are not assigned to you.')
                ->warning()
                ->send();
            return;
        }

        $filename = 'selected_grievances_' . now()->format('Y_m_d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($grievances) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Grievance ID',
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

            foreach ($grievances as $grievance) {
                $submittedBy = $grievance->is_anonymous
                    ? 'Anonymous'
                    : ($grievance->user?->info
                        ? "{$grievance->user->info->first_name} {$grievance->user->info->last_name}"
                        : $grievance->user?->name);

                $departments = $grievance->departments->pluck('department_name')->join(', ') ?: 'N/A';

                fputcsv($handle, [
                    $grievance->grievance_id,
                    $grievance->grievance_title,
                    $grievance->grievance_type,
                    $grievance->grievance_category,
                    $grievance->priority_level,
                    ucfirst(str_replace('_', ' ', $grievance->grievance_status)),
                    $submittedBy,
                    $departments,
                    strip_tags($grievance->grievance_details),
                    $grievance->created_at->format('Y-m-d H:i:s'),
                    $grievance->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function rerouteSelectedGrievances(): void
    {
        $this->validate([
            'selected'   => 'required|array|min:1',
            'department' => 'required|exists:departments,department_name',
            'category'   => 'required|string',
        ]);

        DB::transaction(function () {
            $department = Department::where('department_name', $this->department)->firstOrFail();

            $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $department->department_id))
                ->get();

            foreach ($this->selected as $grievanceId) {
                Assignment::where('grievance_id', $grievanceId)->delete();

                Grievance::where('grievance_id', $grievanceId)->update([
                    'grievance_category' => $this->category,
                ]);

                foreach ($hrLiaisons as $hr) {
                    Assignment::create([
                        'grievance_id'  => $grievanceId,
                        'department_id' => $department->department_id,
                        'assigned_at'   => now(),
                        'hr_liaison_id' => $hr->id,
                    ]);
                }
            }
        });

        Notification::make()
            ->title('Grievances Rerouted')
            ->body('Selected grievances have been rerouted and category updated successfully.')
            ->success()
            ->send();

        $this->redirectRoute('hr-liaison.grievance.index', navigate: true);
    }

    public function updateSelectedGrievanceStatus(): void
    {
        $this->validate([
            'selected' => 'required|array|min:1',
            'status' => 'required|string|in:pending,acknowledged,in_progress,escalated,resolved,unresolved,closed',
        ], [
            'selected.required' => 'Please select at least one grievance to update.',
            'status.required' => 'Please choose a grievance status.',
            'status.in' => 'Invalid grievance status selected.',
        ]);

        DB::beginTransaction();

        try {
            foreach ($this->selected as $grievanceId) {
                $grievance = Grievance::find($grievanceId);

                if ($grievance) {
                    $oldStatus = $grievance->grievance_status;

                    $grievance->update([
                        'grievance_status' => $this->status,
                        'updated_at' => now(),
                    ]);

                    $roleName = ucfirst(auth()->user()->roles->first()?->name ?? 'User');

                    ActivityLog::create([
                        'user_id'      => auth()->id(),
                        'role_id'      => auth()->user()->roles->first()?->id,
                        'module'       => 'Grievance Management',
                        'action'       => "Updated grievance #{$grievance->grievance_id} ({$grievance->grievance_title})",
                        'action_type'  => 'update',
                        'description'  => "{$roleName} ({auth()->user()->email}) updated grievance #{$grievance->grievance_id} from {$oldStatus} to {$this->status}.",
                        'status'       => 'success',
                        'model_type'   => 'App\\Models\\Grievance',
                        'model_id'     => $grievance->grievance_id,
                        'ip_address'   => request()->ip(),
                        'device_info'  => request()->header('User-Agent'),
                        'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
                        'platform'     => php_uname('s'),
                        'location'     => null,
                        'timestamp'    => now(),
                    ]);
                }
            }

            DB::commit();

            $this->dispatch('status-update-success');

            Notification::make()
                ->title('Grievance Status Updated')
                ->body('The selected grievances have been updated to "' . ucwords(str_replace('_', ' ', $this->status)) . '".')
                ->success()
                ->send();

            $this->redirectRoute('hr-liaison.grievance.index', navigate: true);

        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Status Update Failed')
                ->body('An error occurred while updating grievance statuses: ' . $e->getMessage())
                ->danger()
                ->send();
        }
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

    public function printAllGrievances()
    {
        $user = auth()->user();

        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            Notification::make()
                ->title('No Grievances Found')
                ->body('There are no grievances assigned to you to print.')
                ->warning()
                ->send();
            return;
        }

        return redirect()->route('print-all-grievances', ['grievances' => $grievances, 'hr_liaison' => $user]);

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
                    'Grievance ID',
                    'Grievance Title',
                    'Grievance Type',
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
                $grievance->grievance_id,
                $grievance->grievance_title,
                $grievance->grievance_type,
                $grievance->priority_level,
                $grievance->grievance_status,
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

    public function downloadGrievancesCsv()
    {
        $user = auth()->user();

        $grievances = Grievance::with(['user.info', 'departments'])
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            Notification::make()
                ->title('No Grievances Found')
                ->body('There are no grievances assigned to you to export.')
                ->warning()
                ->send();
            return;
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="grievances_assigned_to_' . $user->id . '_' . now()->format('Y_m_d_His') . '.csv"',
        ];

        $callback = function () use ($grievances) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Grievance Ticket ID',
                'Grievance Title',
                'Grievance Type',
                'Priority Level',
                'Status',
                'Submitted By',
                'Departments Involved',
                'Details',
                'Created At',
                'Updated At',
            ]);

            foreach ($grievances as $grievance) {
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
                    $grievance->priority_level,
                    ucfirst(str_replace('_', ' ', $grievance->grievance_status)),
                    $submittedBy,
                    $departments,
                    strip_tags($grievance->grievance_details),
                    $grievance->created_at->format('Y-m-d H:i:s'),
                    $grievance->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

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

    public function goToGrievanceView($id)
    {
        return $this->redirect(route('hr-liaison.grievance.view', $id, absolute: false), navigate: true);
    }

    public function updateStats()
    {
        $user = auth()->user();

        $query = Grievance::whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id));

        if ($this->filterPriority) {
            $query->where('priority_level', $this->filterPriority);
        }

        if ($this->filterStatus && $this->filterStatus !== 'Show All') {
            $map = [
                'Pending' => 'pending',
                'Acknowledged' => 'acknowledged',
                'In Progress' => 'in_progress',
                'Escalated' => 'escalated',
                'Resolved' => 'resolved',
                'Unresolved' => 'unresolved',
                'Closed' => 'closed',
            ];
            if (isset($map[$this->filterStatus])) {
                $query->where('grievance_status', $map[$this->filterStatus]);
            }
        }

        if ($this->filterType) {
            $query->where('grievance_type', $this->filterType);
        }

        if ($this->filterCategory) {
            $query->where('grievance_category', $this->filterCategory);
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

        if ($this->filterIdentity) {
                if ($this->filterIdentity === 'Anonymous') {
                    $query->where('is_anonymous', true);
                } elseif ($this->filterIdentity === 'Not Anonymous') {
                    $query->where('is_anonymous', false);
                }
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
        $user = auth()->user();

        $grievances = Grievance::with(['departments' => fn($q) => $q->distinct(), 'attachments', 'user'])
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
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

                if ($this->filterStatus !== 'Show All' && isset($map[$this->filterStatus])) {
                    $q->where('grievance_status', $map[$this->filterStatus]);
                }
            })
            ->when($this->filterType, fn($q) => $q->where('grievance_type', $this->filterType))
            ->when($this->filterCategory, fn($q) => $q->where('grievance_category', $this->filterCategory))
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
                        ->orWhere('grievance_status', 'like', "%{$normalized}%");
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
            ->when($this->filterIdentity, function($q) {
                if ($this->filterIdentity === 'Anonymous') {
                    $q->where('is_anonymous', true);
                } elseif ($this->filterIdentity === 'Not Anonymous') {
                    $q->where('is_anonymous', false);
                }
            })
            ->when($this->sortField, function($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            }, function($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($this->perPage);

        return view('livewire.user.hr-liaison.grievance.index', compact('grievances'));
    }
}
