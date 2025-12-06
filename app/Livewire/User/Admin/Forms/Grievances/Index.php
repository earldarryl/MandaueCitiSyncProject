<?php

namespace App\Livewire\User\Admin\Forms\Grievances;

use App\Models\GrievanceAttachment;
use App\Models\HrLiaisonDepartment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Grievance;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Browsershot\Browsershot;
#[Layout('layouts.app')]
#[Title('Reports')]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public string $searchInput = '';
    public string $search = '';
    public $importFile;
    public $selectAll = false;
    public $selected = [];
    public $department;
    public $category;
    public $status;
    public $priorityUpdate;
    public $filterPriority;
    public $filterStatus;
    public $filterType;
    public $filterCategory;
    public $filterDepartment;
    public $filterDate;
    public $filterIdentity;
    public $totalGrievances = 0;
    public $criticalPriorityCount = 0;
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
    public $overdueCount;
    public array $departmentOptions = [];
    public array $categoryOptions = [];
    protected $updatesQueryString = [
        'page' => ['except' => 1],
        'search',
        'filterPriority',
        'filterStatus',
        'filterType',
        'filterCategory',
        'filterDepartment',
        'filterDate',
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

        $this->departmentOptions = Department::where('is_active', 1)
            ->where('is_available', 1)
            ->pluck('department_name', 'department_name')
            ->toArray();

        $allCategoryOptions = [
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
        foreach ($allCategoryOptions as $department => $types) {
            foreach ($types as $type => $categories) {
                foreach ($categories as $category) {
                    $flattened[] = $category;
                }
            }
        }

        $customCategories = Grievance::whereNotNull('grievance_category')
            ->pluck('grievance_category')
            ->toArray();

        $allCategories = collect($flattened)
            ->merge($customCategories)
            ->unique()
            ->values()
            ->toArray();

        $this->categoryOptions = array_combine($allCategories, $allCategories);

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

        $grievancesQuery = Grievance::with(['departments', 'user', 'attachments'])
            ->whereIn('grievance_id', $this->selected);

        if (! $user->hasRole('admin')) {
            $grievancesQuery->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id));
        }

        $grievances = $grievancesQuery->get();

        if ($grievances->isEmpty()) {
            Notification::make()
                ->title('No Grievances Found')
                ->body('The selected grievances were not found or are not assigned to you.')
                ->warning()
                ->send();
            return;
        }

        return redirect()->route('print-selected-grievances', [
            'selected' => implode(',', $grievances->pluck('grievance_id')->toArray()),
        ]);
    }

    public function printAllGrievances()
    {
        $admin = auth()->user();

        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            Notification::make()
                ->title('No Grievances Found')
                ->body('There are no grievances available to print.')
                ->warning()
                ->send();
            return;
        }

        return redirect()->route('print-all-grievances', [
            'grievances' => $grievances,
            'admin' => $admin,
        ]);
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
        $admin = auth()->user();
        $adminSlug = str_replace(' ', '_', $admin->name);

        $reports = Grievance::with(['user.info', 'departments'])
            ->latest()
            ->get();

        if ($reports->isEmpty()) {
            Notification::make()
                ->title('No Reports Found')
                ->body('There are no grievance reports to export.')
                ->warning()
                ->send();
            return;
        }

        $filename = "admin-reports-{$adminSlug}-" . now()->format('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($reports) {
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
                'Remarks',
                'Created At',
                'Updated At',
            ]);

            foreach ($reports as $report) {
                $submittedBy = $report->is_anonymous
                    ? 'Anonymous'
                    : ($report->user?->info
                        ? "{$report->user->info->first_name} {$report->user->info->last_name}"
                        : $report->user?->name);

                $departments = $report->departments->pluck('department_name')->join(', ') ?: 'N/A';

                $rawRemarks = $report->grievance_remarks ?? [];
                $remarksArray = is_array($rawRemarks) ? $rawRemarks : json_decode($rawRemarks, true);
                $remarksStr = '';
                if (!empty($remarksArray)) {
                    foreach ($remarksArray as $r) {
                        $remarksStr .= '[' . ($r['timestamp'] ?? '') . '] '
                            . ($r['user_name'] ?? '—') . ' ('
                            . ($r['role'] ?? '—') . '): '
                            . ($r['message'] ?? '') . "\n";
                    }
                } else {
                    $remarksStr = '—';
                }

                fputcsv($handle, [
                    $report->grievance_ticket_id,
                    $report->grievance_title,
                    $report->grievance_type,
                    $report->grievance_category,
                    $report->priority_level,
                    ucfirst(str_replace('_', ' ', $report->grievance_status)),
                    $submittedBy,
                    $departments,
                    strip_tags($report->grievance_details),
                    $remarksStr,
                    $report->created_at->format('Y-m-d H:i:s'),
                    $report->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Total Reports', count($reports)]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateStats()
    {
        $query = Grievance::query();

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
            $query->whereDate('created_at', $this->filterDate);
        }

        if ($this->filterIdentity) {
            if ($this->filterIdentity === 'Anonymous') {
                $query->where('is_anonymous', true);
            } elseif ($this->filterIdentity === 'Not Anonymous') {
                $query->where('is_anonymous', false);
            }
        }

        $this->totalGrievances     = $query->count();
        $this->criticalPriorityCount = (clone $query)->where('priority_level', 'Critical')->count();
        $this->highPriorityCount   = (clone $query)->where('priority_level', 'High')->count();
        $this->normalPriorityCount = (clone $query)->where('priority_level', 'Normal')->count();
        $this->lowPriorityCount    = (clone $query)->where('priority_level', 'Low')->count();

        $this->pendingCount        = (clone $query)->where('grievance_status', 'pending')->count();
        $this->acknowledgedCount   = (clone $query)->where('grievance_status', 'acknowledged')->count();
        $this->inProgressCount     = (clone $query)->where('grievance_status', 'in_progress')->count();
        $this->escalatedCount      = (clone $query)->where('grievance_status', 'escalated')->count();
        $this->resolvedCount       = (clone $query)->where('grievance_status', 'resolved')->count();
        $this->unresolvedCount     = (clone $query)->where('grievance_status', 'unresolved')->count();
        $this->closedCount         = (clone $query)->where('grievance_status', 'closed')->count();
        $this->overdueCount       = (clone $query)->where('grievance_status', 'overdue')->count();
    }

    public function applySearch(): void
    {
        $this->search = trim($this->searchInput);
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
        $this->updateStats();
    }

    public function clearSearch(): void
    {
        $this->reset(['search', 'searchInput']);
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function updatedSelectAll($value)
    {
        $query = Grievance::query()
            ->when($this->filterPriority, fn($q) => $q->where('priority_level', $this->filterPriority))
            ->when($this->filterStatus, fn($q) => $q->where('grievance_status', $this->filterStatus))
            ->when($this->filterType, fn($q) => $q->where('grievance_type', $this->filterType))
            ->when($this->search, function ($query) {
                $term = trim($this->search);
                $query->where(function ($sub) use ($term) {
                    $sub->where('grievance_title', 'like', "%{$term}%")
                        ->orWhere('grievance_details', 'like', "%{$term}%")
                        ->orWhere('priority_level', 'like', "%{$term}%")
                        ->orWhere('grievance_type', 'like', "%{$term}%")
                        ->orWhere('is_anonymous', 'like', "%{$term}%")
                        ->orWhereRaw('CAST(grievance_id AS CHAR) like ?', ["%{$term}%"])
                        ->orWhere('grievance_status', 'like', "%{$term}%");
                });
            });

        $this->selected = $value ? $query->pluck('grievance_id')->toArray() : [];
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

    public function updateSelectedGrievanceStatus(): void
    {
        $this->validate([
            'selected' => 'required|array|min:1',
            'status' => 'required|string|in:pending,acknowledged,in_progress,escalated,resolved,unresolved,closed',
        ]);

        DB::transaction(function () {
            foreach ($this->selected as $grievanceId) {
                $grievance = Grievance::find($grievanceId);

                if ($grievance) {
                    $oldStatus = $grievance->grievance_status;

                    $grievance->update([
                        'grievance_status' => $this->status,
                        'updated_at' => now(),
                    ]);

                    ActivityLog::create([
                        'user_id'      => auth()->id(),
                        'role_id'      => auth()->user()->roles->first()?->id,
                        'module'       => 'Report Management',
                        'action'       => "Updated grievance #{$grievance->grievance_id} ({$grievance->grievance_title})",
                        'action_type'  => 'update',
                        'description'  => "Updated grievance #{$grievance->grievance_id} from {$oldStatus} to {$this->status}.",
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
        });

        Notification::make()
            ->title('Grievance Status Updated')
            ->body('Selected grievances have been updated successfully.')
            ->success()
            ->send();

        $this->redirectRoute('admin.forms.grievances.index', navigate: true);
    }

    public function exportSelectedReportsExcel()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->title('No Reports Selected')
                ->body('Please select at least one report to export.')
                ->warning()
                ->send();
            return;
        }

        $user = auth()->user();
        $userNameSlug = str_replace(' ', '_', $user->name);

        $reports = Grievance::with(['user.info', 'departments', 'attachments'])
            ->whereIn('grievance_id', $this->selected)
            ->latest()
            ->get();

        if ($reports->isEmpty()) {
            Notification::make()
                ->title('No Reports Found')
                ->body('No reports exist for the selected items.')
                ->warning()
                ->send();
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reports');

        $headers = [
            'Report Ticket ID', 'Title', 'Type', 'Category', 'Priority',
            'Status', 'Submitted By', 'Departments', 'Details', 'Attachments', 'Remarks'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . '1', $header);
        }

        $rowNumber = 2;
        foreach ($reports as $report) {
            $submittedBy = $report->is_anonymous
                ? 'Anonymous'
                : ($report->user?->info
                    ? "{$report->user->info->first_name} {$report->user->info->last_name}"
                    : $report->user?->name);

            $departments = $report->departments->pluck('department_name')->join(', ') ?: 'N/A';
            $attachments = $report->attachments->pluck('file_path')->join(', ') ?: 'N/A';

            $remarksArray = $report->grievance_remarks ?? [];
            $remarksStr = '';
            foreach ($remarksArray as $r) {
                $remarksStr .= '[' . ($r['timestamp'] ?? '') . '] '
                    . ($r['user_name'] ?? '—') . ' (' . ($r['role'] ?? '—') . '): '
                    . ($r['message'] ?? '') . "\n";
            }

            if ($remarksStr === '') {
                $remarksStr = '—';
            }

            $values = [
                $report->grievance_ticket_id,
                $report->grievance_title,
                $report->grievance_type,
                $report->grievance_category,
                $report->priority_level,
                ucfirst(str_replace('_', ' ', $report->grievance_status)),
                $submittedBy,
                $departments,
                strip_tags($report->grievance_details),
                $attachments,
                $remarksStr
            ];

            foreach ($values as $col => $value) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . $rowNumber, $value);
            }

            $rowNumber++;
        }

        $filename = "reports-{$userNameSlug}-" . now()->format('Y_m_d_His') . ".xlsx";
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }


    public function downloadReportsExcel()
    {
        $user = auth()->user();
        $userNameSlug = str_replace(' ', '_', $user->name);

        $reports = Grievance::with(['user.info', 'departments', 'attachments'])
            ->latest()
            ->get();

        if ($reports->isEmpty()) {
            Notification::make()
                ->title('No Reports Found')
                ->body('There are no reports in the system.')
                ->warning()
                ->send();
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reports');

        $headers = [
            'Report Ticket ID', 'Title', 'Type', 'Category', 'Priority',
            'Status', 'Submitted By', 'Departments', 'Details', 'Attachments', 'Remarks'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . '1', $header);
        }

        $rowNumber = 2;
        foreach ($reports as $report) {

            $submittedBy = $report->is_anonymous
                ? 'Anonymous'
                : ($report->user?->info
                    ? "{$report->user->info->first_name} {$report->user->info->last_name}"
                    : $report->user?->name);

            $departments = $report->departments->pluck('department_name')->join(', ') ?: 'N/A';
            $attachments = $report->attachments->pluck('file_path')->join(', ') ?: 'N/A';

            $remarksArray = $report->grievance_remarks ?? [];
            $remarksStr = '';

            foreach ($remarksArray as $r) {
                $remarksStr .= '[' . ($r['timestamp'] ?? '') . '] '
                    . ($r['user_name'] ?? '—') . ' (' . ($r['role'] ?? '—') . '): '
                    . ($r['message'] ?? '') . "\n";
            }

            if ($remarksStr === '') {
                $remarksStr = '—';
            }

            $values = [
                $report->grievance_ticket_id,
                $report->grievance_title,
                $report->grievance_type,
                $report->grievance_category,
                $report->priority_level,
                ucfirst(str_replace('_', ' ', $report->grievance_status)),
                $submittedBy,
                $departments,
                strip_tags($report->grievance_details),
                $attachments,
                $remarksStr
            ];

            foreach ($values as $col => $value) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . $rowNumber, $value);
            }

            $rowNumber++;
        }

        $filename = "reports-{$userNameSlug}-" . now()->format('Y_m_d_His') . ".xlsx";
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function importReportsExcel()
    {
        if (!$this->importFile) {
            Notification::make()
                ->title('No File Selected')
                ->body('Please select a Reports Excel file to import.')
                ->warning()
                ->send();
            return;
        }

        $currentUser = auth()->user();

        try {

            $path = $this->importFile->store('temp_import', 'public');
            $fullPath = Storage::disk('public')->path($path);
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) <= 1) {
                Notification::make()
                    ->title('Empty File')
                    ->body('The uploaded Excel file contains no report records.')
                    ->warning()
                    ->send();
                Storage::disk('public')->delete($path);
                return;
            }

            unset($rows[0]);
            $skippedCount = 0;

            foreach ($rows as $row) {
                [
                    $ticketId, $title, $type, $category, $priority,
                    $status, $submittedBy, $departments, $details, $attachmentsColumn, $remarksColumn
                ] = array_pad($row, 11, null);

                $existingReport = Grievance::withTrashed()
                    ->where('grievance_ticket_id', $ticketId)
                    ->first();

                $userId = $existingReport?->user_id ?? $currentUser->id;

                $processingDays = match (strtolower($priority)) {
                    'low' => 3,
                    'normal' => 7,
                    'high' => 20,
                    'critical' => 7,
                    default => 7,
                };

                $report = Grievance::updateOrCreate(
                    ['grievance_ticket_id' => $ticketId],
                    [
                        'user_id' => $userId,
                        'grievance_type' => $type,
                        'grievance_category' => $category,
                        'priority_level' => $priority,
                        'grievance_title' => $title,
                        'grievance_details' => $details,
                        'is_anonymous' => strtolower($submittedBy) === 'anonymous',
                        'grievance_status' => strtolower($status),
                        'processing_days' => $processingDays,
                    ]
                );

                $departmentNames = explode(',', $departments);
                foreach ($departmentNames as $deptName) {
                    $department = Department::where('department_name', trim($deptName))->first();
                    if (!$department) continue;

                    if (!$report->department_id) {
                        $report->update([
                            'department_id' => $department->department_id
                        ]);
                    }

                    $hrUsers = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                        ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $department->department_id))
                        ->get();

                    foreach ($hrUsers as $hr) {
                        Assignment::updateOrCreate([
                            'grievance_id' => $report->grievance_id,
                            'department_id' => $department->department_id,
                            'hr_liaison_id' => $hr->id,
                        ], [
                            'assigned_at' => now(),
                        ]);
                    }
                }

                $attachmentFiles = $attachmentsColumn ? explode(',', $attachmentsColumn) : [];
                foreach ($attachmentFiles as $fileName) {
                    $fileName = trim($fileName);
                    if ($fileName) {
                        GrievanceAttachment::updateOrCreate(
                            [
                                'grievance_id' => $report->grievance_id,
                                'file_name' => $fileName,
                            ],
                            [
                                'file_path' => $fileName,
                            ]
                        );
                    }
                }

                $remarksArray = [];
                if ($remarksColumn) {
                    $lines = explode("\n", $remarksColumn);
                    foreach ($lines as $line) {
                        if (preg_match('/\[(.*?)\]\s*(.*?)\s*\((.*?)\):\s*(.*)/', $line, $matches)) {
                            $remarksArray[] = [
                                'timestamp' => $matches[1] ?? null,
                                'user_name' => $matches[2] ?? null,
                                'role' => $matches[3] ?? null,
                                'message' => $matches[4] ?? null,
                            ];
                        }
                    }
                }

                if (!empty($remarksArray)) {
                    $report->update(['grievance_remarks' => $remarksArray]);
                }

            }

            $this->resetPage();
            $this->updateStats();

            Notification::make()
                ->title('Import Completed')
                ->body('Reports imported successfully.' . ($skippedCount ? " Skipped {$skippedCount} duplicates." : ""))
                ->success()
                ->send();

            Storage::disk('public')->delete($path);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Import Failed')
                ->body("Error: {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }

    public function downloadSelectedGrievancesPdf()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->title('No Reports Selected')
                ->body('Please select at least one report to download.')
                ->warning()
                ->send();
            return;
        }

        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->whereIn('grievance_id', $this->selected)
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            Notification::make()
                ->title('No Reports Found')
                ->body('The selected reports were not found.')
                ->warning()
                ->send();
            return;
        }

        $html = view('pdf.selected-grievances', [
            'grievances' => $grievances,
            'hr_liaison' => auth()->user(),
        ])->render();

        $filename = 'selected-reports-' . now()->format('Ymd-His') . '.pdf';
        $pdfPath = storage_path("app/public/{$filename}");

        Browsershot::html($html)
            ->setNodeBinary('C:\Program Files\nodejs\node.exe')
            ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->delay(1500)
            ->timeout(120)
            ->format('A4')
            ->save($pdfPath);

        return response()->download($pdfPath, $filename);
    }

    public function downloadAllGrievancesPdf()
    {
        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            Notification::make()
                ->title('No Reports Found')
                ->body('There are no grievances available to download.')
                ->warning()
                ->send();
            return;
        }

        $html = view('pdf.all-grievances', [
            'grievances' => $grievances,
            'hr_liaison' => auth()->user(),
        ])->render();

        $filename = 'all-reports-' . now()->format('Ymd-His') . '.pdf';
        $pdfPath = storage_path("app/public/{$filename}");

        Browsershot::html($html)
            ->setNodeBinary('C:\Program Files\nodejs\node.exe')
            ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->delay(1500)
            ->timeout(120)
            ->format('A4')
            ->save($pdfPath);

        return response()->download($pdfPath, $filename);
    }

    public function exportSelectedGrievancesCsv()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->title('No Reports Selected')
                ->body('Please select at least one report to export.')
                ->warning()
                ->send();
            return;
        }

        $admin = auth()->user();
        $adminSlug = str_replace(' ', '_', $admin->name);

    $reports = Grievance::with(['user.info', 'departments'])
        ->whereIn('grievance_id', $this->selected)
        ->latest()
        ->get();

    if ($reports->isEmpty()) {
        Notification::make()
            ->title('No Reports Found')
            ->body('The selected reports were not found in the system.')
            ->warning()
            ->send();
        return;
    }

    $filename = "admin-selected-reports-{$adminSlug}-" . now()->format('Y_m_d_His') . ".csv";
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function () use ($reports) {
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
            'Remarks',
            'Created At',
            'Updated At',
        ]);

        foreach ($reports as $report) {
            $submittedBy = $report->is_anonymous
                ? 'Anonymous'
                : ($report->user?->info
                    ? "{$report->user->info->first_name} {$report->user->info->last_name}"
                    : $report->user?->name);

            $departments = $report->departments->pluck('department_name')->join(', ') ?: 'N/A';

            $rawRemarks = $report->grievance_remarks ?? [];
            $remarksArray = is_array($rawRemarks) ? $rawRemarks : json_decode($rawRemarks, true);
            $remarksStr = '';
            if (!empty($remarksArray)) {
                foreach ($remarksArray as $r) {
                    $remarksStr .= '[' . ($r['timestamp'] ?? '') . '] '
                        . ($r['user_name'] ?? '—') . ' ('
                        . ($r['role'] ?? '—') . '): '
                        . ($r['message'] ?? '') . "\n";
                }
            } else {
                $remarksStr = '—';
            }

            fputcsv($handle, [
                $report->grievance_ticket_id,
                $report->grievance_title,
                $report->grievance_type,
                $report->grievance_category,
                $report->priority_level,
                ucfirst(str_replace('_', ' ', $report->grievance_status)),
                $submittedBy,
                $departments,
                strip_tags($report->grievance_details),
                $remarksStr,
                $report->created_at->format('Y-m-d H:i:s'),
                $report->updated_at->format('Y-m-d H:i:s'),
            ]);
        }

        fputcsv($handle, []);
        fputcsv($handle, ['Total Reports', count($reports)]);

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

        $user = auth()->user();

        DB::transaction(function () use ($user) {
            $department = Department::where('department_name', $this->department)->firstOrFail();

            foreach ($this->selected as $grievanceId) {
                $grievance = Grievance::findOrFail($grievanceId);

                $oldStatus      = $grievance->grievance_status;
                $oldCategory    = $grievance->grievance_category;
                $oldDepartments = $grievance->departments()->pluck('department_name')->toArray();

                $grievance->assignments()->delete();

                $hrLiaisons = HrLiaisonDepartment::where('department_id', $department->department_id)
                    ->pluck('hr_liaison_id')
                    ->toArray();

                if (empty($hrLiaisons)) {
                    Notification::make()
                        ->title('No HR Liaisons Found')
                        ->body("Department {$department->department_name} has no HR Liaisons assigned for report #{$grievance->grievance_ticket_id}.")
                        ->danger()
                        ->send();

                    continue;
                }

                $grievance->update([
                    'department_id'      => $department->department_id,
                    'grievance_status'   => 'pending',
                    'grievance_category' => $this->category,
                    'updated_at'         => now(),
                ]);

                foreach ($hrLiaisons as $liaisonId) {
                    Assignment::create([
                        'grievance_id'  => $grievance->grievance_id,
                        'department_id' => $department->department_id,
                        'hr_liaison_id' => $liaisonId,
                        'assigned_at'   => now(),
                    ]);
                }

                $changes = [
                    'grievance_status' => [
                        'old' => ucfirst($oldStatus),
                        'new' => 'Pending',
                    ],
                    'grievance_category' => [
                        'old' => $oldCategory,
                        'new' => $this->category,
                    ],
                    'departments' => [
                        'old' => implode(', ', $oldDepartments),
                        'new' => $department->department_name,
                    ],
                    'assigned_hr_liaisons' => [
                        'new' => implode(', ', User::whereIn('id', $hrLiaisons)->pluck('name')->toArray()),
                    ],
                ];

                ActivityLog::create([
                    'user_id'      => $user->id,
                    'role_id'      => $user->roles->first()?->id,
                    'module'       => 'Report Management',
                    'action'       => "Rerouted report #{$grievance->grievance_ticket_id} to {$department->department_name}",
                    'action_type'  => 'reroute',
                    'model_type'   => 'App\\Models\\Grievance',
                    'model_id'     => $grievance->grievance_id,
                    'description'  => "Assigned HR Liaisons: " . implode(', ', User::whereIn('id', $hrLiaisons)->pluck('name')->toArray()),
                    'changes'      => $changes,
                    'status'       => 'success',
                    'ip_address'   => request()->ip(),
                    'device_info'  => request()->header('User-Agent'),
                    'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
                    'platform'     => php_uname('s'),
                    'timestamp'    => now(),
                ]);
            }
        });

        Notification::make()
            ->title('Reports Rerouted')
            ->body('Selected reports have been rerouted, category updated, and status set to pending successfully.')
            ->success()
            ->send();

        $this->redirectRoute('admin.forms.grievances.index', navigate: true);
    }

    public function updateSelectedPriority(): void
    {
        $this->validate([
            'selected' => 'required|array|min:1',
            'priorityUpdate' => 'required|string',
        ], [
            'selected.required' => 'Please select at least one grievance to update.',
            'priorityUpdate.required' => 'Please choose a priority level.',
        ]);

        $user = auth()->user();
        $formattedPriority = $this->priorityUpdate;

        $priorityProcessingDays = match (strtolower($formattedPriority)) {
            'low'      => 3,
            'normal'   => 7,
            'high'     => 20,
            'critical' => 7,
            default    => 7,
        };

        DB::beginTransaction();

        try {
            foreach ($this->selected as $grievanceId) {
                $grievance = Grievance::find($grievanceId);

                if ($grievance) {
                    $oldPriority = $grievance->priority_level;
                    $oldProcessingDays = $grievance->processing_days;

                    $grievance->update([
                        'priority_level'  => $formattedPriority,
                        'processing_days' => $priorityProcessingDays,
                        'updated_at'      => now(),
                    ]);

                    ActivityLog::create([
                        'user_id'      => $user->id,
                        'role_id'      => $user->roles->first()?->id,
                        'module'       => 'Report Management',
                        'action'       => "Changed report #{$grievance->grievance_id} priority from {$oldPriority} to {$formattedPriority} and processing days from {$oldProcessingDays} to {$priorityProcessingDays}",
                        'action_type'  => 'update_priority',
                        'model_type'   => 'App\\Models\\Grievance',
                        'model_id'     => $grievance->grievance_id,
                        'description'  => "HR Liaison ({$user->email}) changed priority of report #{$grievance->grievance_id} from {$oldPriority} to {$formattedPriority}, updating processing days from {$oldProcessingDays} to {$priorityProcessingDays}.",
                        'status'       => 'success',
                        'ip_address'   => request()->ip(),
                        'device_info'  => request()->header('User-Agent'),
                        'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
                        'platform'     => php_uname('s'),
                        'timestamp'    => now(),
                    ]);
                }
            }

            DB::commit();

            $this->dispatch('priority-update-success');

            Notification::make()
                ->title('Priority Updated')
                ->body('The selected grievances have been updated to "' . ucfirst($formattedPriority) . '" with updated processing days.')
                ->success()
                ->send();

            $this->redirectRoute('admin.forms.grievances.index', navigate: true);

        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Priority Update Failed')
                ->body('An error occurred while updating grievance priorities: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        $grievances = Grievance::with(['departments', 'attachments', 'user'])
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
                    'Overdue' => 'overdue',
                ];
                if (isset($map[$this->filterStatus])) {
                    $q->where('grievance_status', $map[$this->filterStatus]);
                }
            })
            ->when($this->filterType, fn($q) => $q->where('grievance_type', $this->filterType))
            ->when($this->filterCategory, fn($q) => $q->where('grievance_category', $this->filterCategory))
            ->when($this->filterDepartment, fn($q) =>
                $q->whereHas('departments', fn($sub) =>
                    $sub->where('department_name', $this->filterDepartment)
                )
            )
            ->when($this->search, function ($query) {
                $term = trim($this->search);
                $query->where(function ($sub) use ($term) {
                    $sub->where('grievance_title', 'like', "%{$term}%")
                        ->orWhere('grievance_details', 'like', "%{$term}%")
                        ->orWhere('priority_level', 'like', "%{$term}%")
                        ->orWhere('grievance_type', 'like', "%{$term}%")
                        ->orWhere('grievance_category', 'like', "%{$term}%")
                        ->orWhere('is_anonymous', 'like', "%{$term}%")
                        ->orWhereRaw('CAST(grievance_ticket_id AS CHAR) like ?', ["%{$term}%"])
                        ->orWhere('grievance_status', 'like', "%{$term}%")
                        ->orWhereHas('departments', fn($d) => $d->where('department_name', 'like', "%{$term}%"));
                });
            })
            ->when($this->filterDate, function ($q) {
                $q->whereDate('created_at', $this->filterDate);
            })
            ->orderBy($this->sortField ?? 'created_at', $this->sortDirection ?? 'desc')
            ->paginate($this->perPage ?? 10);

        return view('livewire.user.admin.forms.grievances.index', [
            'grievances' => $grievances,
        ]);
    }

}
