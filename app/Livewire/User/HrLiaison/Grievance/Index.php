<?php

namespace App\Livewire\User\HrLiaison\Grievance;

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

    public $perPage = 4;
    public $search = '';
    public $searchInput = '';
    public $filterPriority = '';
    public $filterStatus = '';
    public $filterType = '';
    public $filterDate = '';
    public $selectAll = false;
    public $selected = [];
    public $department = [];
    public $departmentOptions;

    public $totalGrievances = 0;
    public $highPriorityCount = 0;
    public $normalPriorityCount = 0;
    public $lowPriorityCount = 0;
    public $pendingCount = 0;
    public $inProgressCount = 0;
    public $resolvedCount = 0;
    public $rejectedCount = 0;

    protected $updatesQueryString = [
        'search' => ['except' => ''],
        'filterPriority' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterDate' => ['except' => ''],
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

        $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->pluck('department_name', 'department_id')
            ->toArray();

    }

    public function updatedSelectAll($value)
    {
        $user = auth()->user();

        $query = Grievance::whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->when($this->filterPriority, fn($q) => $q->where('priority_level', $this->filterPriority))
            ->when($this->filterStatus, function ($q) {
                $map = [
                    'Pending' => 'pending',
                    'In Progress' => 'in_progress',
                    'Resolved' => 'resolved',
                    'Rejected' => 'rejected',
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
            'selected' => 'required|array|min:1',
            'department' => 'required|array|min:1',
        ], [
            'selected.required' => 'Please select at least one grievance to reroute.',
            'department.required' => 'Please choose at least one department for rerouting.',
        ]);

        DB::beginTransaction();

        try {
            $liaisonsByDept = [];
            foreach ($this->department as $deptId) {
                $liaisonsByDept[$deptId] = User::whereHas('roles', fn($q) =>
                        $q->where('name', 'hr_liaison')
                    )
                    ->whereHas('departments', fn($q) =>
                        $q->where('hr_liaison_departments.department_id', $deptId)
                    )
                    ->get();
            }

            foreach ($this->selected as $grievanceId) {
                Assignment::where('grievance_id', $grievanceId)->delete();

                foreach ($liaisonsByDept as $deptId => $hrLiaisons) {
                    foreach ($hrLiaisons as $hr) {
                        Assignment::create([
                            'grievance_id'  => $grievanceId,
                            'department_id' => $deptId,
                            'assigned_at'   => now(),
                            'hr_liaison_id' => $hr->id,
                        ]);
                    }
                }
            }

            DB::commit();

            Notification::make()
                ->title('Grievances Rerouted Successfully')
                ->body('All selected grievances have been reassigned to the selected department(s) and their HR liaisons.')
                ->success()
                ->send();

            $this->redirectRoute('hr-liaison.grievance.index', navigate: true);

        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Rerouting Failed')
                ->body('An unexpected error occurred while rerouting grievances: ' . $e->getMessage())
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

    public function print($id)
    {
        return redirect()->route('print-grievance', ['id' => $id]);
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

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPriority() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterType() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }

    public function applySearch()
    {
        $this->search = $this->searchInput;
        $this->resetPage();
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

        if ($this->filterStatus) {
            $map = [
                'Pending' => 'pending',
                'In Progress' => 'in_progress',
                'Resolved' => 'resolved',
                'Rejected' => 'rejected',
            ];
            if (isset($map[$this->filterStatus])) {
                $query->where('grievance_status', $map[$this->filterStatus]);
            }
        }

        if ($this->filterType) {
            $query->where('grievance_type', $this->filterType);
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
        $this->inProgressCount   = (clone $query)->where('grievance_status', 'in_progress')->count();
        $this->resolvedCount     = (clone $query)->where('grievance_status', 'resolved')->count();
        $this->rejectedCount     = (clone $query)->where('grievance_status', 'rejected')->count();
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
                    'In Progress' => 'in_progress',
                    'Resolved' => 'resolved',
                    'Rejected' => 'rejected',
                ];
                if(isset($map[$this->filterStatus])) $q->where('grievance_status', $map[$this->filterStatus]);
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
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.user.hr-liaison.grievance.index', compact('grievances'));
    }
}
