<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\Grievance;
use App\Models\GrievanceAttachment;
use App\Models\GrievanceReroute;
use App\Models\HistoryLog;
use App\Models\HrLiaisonDepartment;
use App\Models\User;
use App\Notifications\GeneralNotification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\URL;
#[Layout('layouts.app')]
#[Title('Assignment Reports')]
class Index extends Component
{
    use WithPagination, WithFileUploads;


    protected $paginationTheme = 'tailwind';
    public ?string $sortField = null;
    public string $sortDirection = 'asc';
    public $perPage = 10;
    public $search = '';
    public $searchInput = '';
    public $importFile;
    public $filterPriority = '';
    public $filterStatus = '';
    public $filterType = '';
    public $filterCategory = '';
    public $filterDate = '';
    public $filterIdentity = '';
    public $filterRerouteStatus = '';
    public $filterFromDepartment = '';
    public $filterToDepartment = '';
    public $filterRerouteCategory = '';
    public $selectAll = false;
    public $selected = [];
    public $department;
    public $category;
    public $status;
    public $priorityUpdate;
    public $departmentOptions;
    public $departmentRerouteOptions;
    public $categoryOptions;
    public $grievanceRerouteCategories;
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
    protected $updatesQueryString = [
        'search' => ['except' => ''],
        'filterPriority' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterDate' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'page'           => ['except' => 1],
    ];

    protected $listeners = [
        'poll' => '$refresh',
    ];

    public string $rerouteSortField = 'created_at';
    public string $rerouteSortDirection = 'desc';
    public int $reroutePerPage = 10;
    public string $searchReroutesInput = '';
    public string $searchReroutes = '';

    public function sortReroutesBy(string $field)
    {
        if ($this->rerouteSortField === $field) {
            $this->rerouteSortDirection =
                $this->rerouteSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->rerouteSortField = $field;
            $this->rerouteSortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function applySearchReroutes()
    {
        $this->searchReroutes = trim($this->searchReroutesInput);
        $this->resetPage('reroutesPage');
    }

    public function clearSearchReroutes()
    {
        $this->searchReroutesInput = '';
        $this->searchReroutes = '';
        $this->resetPage('reroutesPage');
    }

    private function displayRoleName(string $role): string
    {
        return match ($role) {
            'hr_liaison' => 'HR Liaison',
            'admin'      => 'Administrator',
            'citizen'    => 'Citizen',
            default      => ucwords(str_replace('_', ' ', $role)),
        };
    }

    private function formatStatus($value)
    {
        return strtolower(str_replace(' ', '_', trim($value)));
    }

    private function displayText($value)
    {
        return ucwords(str_replace('_', ' ', $value));
    }

    public function resetInputFieldsByCancelModal(): void
    {

        $this->status = null;
        $this->priorityUpdate = null;
        $this->department = null;
        $this->category = null;
        $this->importFile = null;

        $this->resetErrorBag();
        $this->resetValidation();
        $this->dispatch('reset-reroute-form');

    }

    public function resetInputFields(): void
    {
        $this->selected = [];
        $this->status = null;
        $this->priorityUpdate = null;
        $this->department = null;
        $this->category = null;
        $this->importFile = null;

        $this->resetErrorBag();
        $this->resetValidation();
        $this->dispatch('reset-reroute-form');

    }

    public function mount()
    {
        $this->updateStats();

        if (session()->has('notification')) {
            $notif = session('notification');

            $this->dispatch('notify', [
                'type' => $notif['type'],
                'title' => $notif['title'],
                'message' => $notif['body'],
            ]);
        }

        $currentHrLiaison = auth()->user();

        $liaisonDepartments = $currentHrLiaison->departments->pluck('department_name')->toArray();

        $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->whereNotIn('department_name', $liaisonDepartments)
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->pluck('department_name', 'department_name')
            ->toArray();

        $this->departmentRerouteOptions = Department::whereHas('hrLiaisons')
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->pluck('department_name', 'department_name')
            ->toArray();

        $departmentName = $currentHrLiaison->departments->first()->department_name ?? null;

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

        $departmentCategories = $allCategoryOptions[$departmentName] ?? [];

        $flattened = [];
        foreach ($departmentCategories as $type => $categories) {
            foreach ($categories as $category) {
                $flattened[] = $category;
            }
        }

        $customCategories = Grievance::whereHas('assignments', function ($q) use ($currentHrLiaison) {
                $departmentId = $currentHrLiaison->departments->first()->department_id ?? null;
                $q->where('department_id', $departmentId);
            })
            ->whereNotNull('grievance_category')
            ->pluck('grievance_category')
            ->toArray();

        $allCategories = collect($flattened)
            ->merge($customCategories)
            ->unique()
            ->values()
            ->toArray();

        $this->categoryOptions = array_combine($allCategories, $allCategories);

        $this->grievanceRerouteCategories = Grievance::distinct('grievance_category')
            ->pluck('grievance_category')
            ->filter()
            ->toArray();
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
                    'Overdue' => 'overdue'
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
                        ->orWhereRaw('CAST(grievance_ticket_id AS CHAR) like ?', ["%{$term}%"])
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
        // $this->selectAll = false;
        // $this->selected = [];
    }

    public function exportSelectedReportsExcel()
    {
        if (empty($this->selected)) {

            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => 'Please select at least one report to export.',
            ]);
            return;
        }

        $user = auth()->user();
        $userNameSlug = str_replace(' ', '_', $user->name);

        $reports = Grievance::with(['user.info', 'departments', 'attachments'])
            ->whereIn('grievance_id', $this->selected)
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($reports->isEmpty()) {

            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => 'The selected reports were not found or are not assigned to you.',
            ]);
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reports');

        $headers = [
            'User ID',
            'Report Ticket ID', 'Title', 'Type', 'Category', 'Priority',
            'Status', 'Submitted By', 'Departments', 'Details', 'Attachments', 'Remarks', 'Created At', 'Updated At'
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
            if (!empty($remarksArray)) {
                foreach ($remarksArray as $r) {
                    $remarksStr .= '[' . ($r['timestamp'] ?? '') . '] ' .
                        ($r['user_name'] ?? '—') . ' (' . ($r['role'] ?? '—') . '): ' .
                        ($r['message'] ?? '') . "\n";
                }
            } else {
                $remarksStr = '—';
            }

            $values = [
                $report->user_id,
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
                $remarksStr,
                $report->created_at?->format('Y-m-d H:i:s') ?? '',
                $report->updated_at?->format('Y-m-d H:i:s') ?? '',
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
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($reports->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => 'There are no reports assigned to you to export.',
            ]);
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reports');

        $headers = [
            'User ID',
            'Report Ticket ID', 'Title', 'Type', 'Category', 'Priority',
            'Status', 'Submitted By', 'Departments', 'Details', 'Attachments', 'Remarks', 'Created At', 'Updated At'
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
            if (!empty($remarksArray)) {
                foreach ($remarksArray as $r) {
                    $remarksStr .= '[' . ($r['timestamp'] ?? '') . '] ' .
                        ($r['user_name'] ?? '—') . ' (' . ($r['role'] ?? '—') . '): ' .
                        ($r['message'] ?? '') . "\n";
                }
            } else {
                $remarksStr = '—';
            }

            $values = [
                $report->user_id,
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
                $remarksStr,
                $report->created_at?->format('Y-m-d H:i:s') ?? '',
                $report->updated_at?->format('Y-m-d H:i:s') ?? '',
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
            $this->dispatch('notify', [
                'type' => 'info',
                'title' => 'No File Selected',
                'message' => 'Please select a Reports Excel file to import.',
            ]);
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
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'title' => 'Empty File',
                    'message' => 'The uploaded Excel file contains no report records.',
                ]);
                Storage::disk('public')->delete($path);
                return;
            }

            unset($rows[0]);
            $skippedCount = 0;

            foreach ($rows as $row) {
                [
                    $userIdColumn,
                    $ticketId, $title, $type, $category, $priority,
                    $status, $submittedBy, $departments, $details, $attachmentsColumn, $remarksColumn, $createdAtColumn, $updatedAtColumn
                ] = array_pad($row, 11, null);

                $existingReport = Grievance::withTrashed()->where('grievance_ticket_id', $ticketId)->first();
                if ($existingReport) {
                    $hasAssignments = Assignment::where('grievance_id', $existingReport->grievance_id)->exists();
                    if ($hasAssignments) {
                        $skippedCount++;
                        continue;
                    }
                }

                $userId = User::where('id', $userIdColumn)->exists()
                        ? $userIdColumn
                        : $currentUser->id;


                $processingDays = match (strtolower($priority)) {
                    'low'      => 3,
                    'normal'   => 7,
                    'high'     => 20,
                    'critical' => 7,
                    default    => 7,
                };

                $createdAt = $createdAtColumn ? \Carbon\Carbon::parse($createdAtColumn) : now();
                $updatedAt = $updatedAtColumn ? \Carbon\Carbon::parse($updatedAtColumn) : now();

                $report = Grievance::updateOrCreate(
                    ['grievance_ticket_id' => $ticketId],
                    [
                        'user_id' => $userId,
                        'grievance_type' => $type,
                        'grievance_category' => $category,
                        'priority_level' => $priority,
                        'grievance_title' => $title,
                        'grievance_details' => $details,
                        'is_anonymous' => strtolower($submittedBy) === 'anonymous' ? 1 : 0,
                        'grievance_status' => strtolower($status) === 'pending' ? 'pending' : strtolower($status),
                        'processing_days' => $processingDays,
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                    ]
                );

                $departmentNames = explode(',', $departments);
                foreach ($departmentNames as $deptName) {
                    $department = Department::where('department_name', trim($deptName))->first();
                    if ($department && $department->is_active && $department->is_available) {
                        if (!$report->department_id) {
                            $report->update([
                                'department_id' => $department->department_id
                            ]);
                        }

                        $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                            ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $department->department_id))
                            ->get();

                        foreach ($hrLiaisons as $hr) {
                            $exists = Assignment::where([
                                'grievance_id' => $report->grievance_id,
                                'department_id' => $department->department_id,
                                'hr_liaison_id' => $hr->id,
                            ])->exists();

                            if (!$exists) {
                                Assignment::create([
                                    'grievance_id' => $report->grievance_id,
                                    'department_id' => $department->department_id,
                                    'hr_liaison_id' => $hr->id,
                                    'assigned_at' => now(),
                                ]);
                            }
                        }
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

                $this->resetPage();
                $this->updateStats();

                HistoryLog::create([
                    'user_id' => $currentUser->id,
                    'action_type' => 'report_import',
                    'description' => "Imported report titled '{$title}' from Excel.",
                    'reference_id' => $report->grievance_id,
                    'reference_table' => 'reports',
                    'ip_address' => request()->ip(),
                ]);

                ActivityLog::create([
                    'user_id' => $currentUser->id,
                    'role_id' => $currentUser->roles->first()?->id,
                    'module' => 'Report Management',
                    'action' => "Imported report #{$report->grievance_ticket_id}",
                    'action_type' => 'import',
                    'model_type' => Grievance::class,
                    'model_id' => $report->grievance_id,
                    'description' => "{$currentUser->email} imported report #{$report->grievance_ticket_id} from Excel.",
                    'status' => 'success',
                    'ip_address' => request()->ip(),
                    'device_info' => request()->header('User-Agent'),
                    'user_agent' => substr(request()->header('User-Agent'), 0, 255),
                    'platform' => php_uname('s'),
                    'location' => geoip(request()->ip())?->city,
                    'timestamp' => now(),
                ]);
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Import Completed',
                'message' => 'Reports have been imported successfully.' . ($skippedCount ? " Skipped {$skippedCount} duplicate(s)." : ""),
            ]);

            Storage::disk('public')->delete($path);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Import Failed',
                'message' => "Something went wrong while importing reports. Error: {$e->getMessage()}.",
            ]);
        }
    }

    public function printSelectedGrievances()
    {
        if (empty($this->selected)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => "Please select at least one grievance to print.",
            ]);
            return;
        }

        $user = auth()->user();

        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->whereIn('grievance_id', $this->selected)
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->get();

        if ($grievances->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => "The selected reports were not found or are not assigned to you.",
            ]);
            return;
        }

        return redirect()->route('print-selected-grievances', [
            'selected' => implode(',', $this->selected),
        ]);
    }

    public function exportSelectedReportsCsv()
    {
        if (empty($this->selected)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Selected',
                'message' => "Please select at least one report to export.",
            ]);
            return;
        }

        $user = auth()->user();
        $userNameSlug = str_replace(' ', '_', $user->name);

        $reports = Grievance::with(['user.info', 'departments'])
            ->whereIn('grievance_id', $this->selected)
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($reports->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => "The selected reports were not found or are not assigned to you.",
            ]);
            return;
        }

        $filename = "selected-reports-{$userNameSlug}-" . now()->format('Y_m_d_His') . ".csv";
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
                        $remarksStr .= '[' . ($r['timestamp'] ?? '') . '] ' . ($r['user_name'] ?? '—') .
                            ' (' . ($r['role'] ?? '—') . '): ' . ($r['message'] ?? '') . "\n";
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
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'title' => 'No HR Liaisons Found',
                        'message' => "Department {$department->department_name} has no HR Liaisons assigned for report #{$grievance->grievance_ticket_id}.",
                    ]);
                    continue;
                }

                $oldDepartmentId = $grievance->department_id;

                $grievance->update([
                    'department_id'      => $department->department_id,
                    'grievance_status'   => 'pending',
                    'grievance_category' => $this->category,
                    'updated_at'         => now(),
                ]);

                GrievanceReroute::create([
                    'grievance_id'       => $grievance->grievance_id,
                    'from_department_id' => $oldDepartmentId,
                    'to_department_id'   => $department->department_id,
                    'performed_by'       => $user->id,
                    'from_category'      => $oldCategory,
                    'to_category'        => $this->category,
                ]);

                foreach ($hrLiaisons as $liaisonId) {
                    Assignment::create([
                        'grievance_id'  => $grievance->grievance_id,
                        'department_id' => $department->department_id,
                        'hr_liaison_id' => $liaisonId,
                        'assigned_at'   => now(),
                    ]);
                }

                $grievance->addRemark([
                    'message'   => "Report rerouted to '{$department->department_name}' and category changed to '{$this->category}' by {$user->name} (" . $this->displayRoleName($user->getRoleNames()->first()) .").",
                    'user_id'   => $user->id,
                    'user_name' => $user->name,
                    'role'      => $this->displayRoleName($user->getRoleNames()->first()),
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                    'status'    => 'pending',
                    'type'      => 'reroute',
                ]);

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

                $citizen = $grievance->user()->first();
                if ($citizen) {
                    $citizen->notify(new GeneralNotification(
                        'Your Report Was Rerouted',
                        "Your report '{$grievance->grievance_title}' has been rerouted to {$department->department_name}.",
                        'info',
                        ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                        ['type' => 'info'],
                        true,
                        [
                            [
                                'label' => 'View Updated Report',
                                'url'   => route('citizen.grievance.view', $grievance->grievance_ticket_id),
                                'open_new_tab' => true,
                            ],
                        ]
                    ));
                }

                $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
                foreach ($admins as $admin) {
                    $admin->notify(new GeneralNotification(
                        'Report Rerouted',
                        "The report '{$grievance->grievance_title}' was rerouted to {$department->department_name}.",
                        'warning',
                        ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                        ['type' => 'warning'],
                        true,
                        [
                            [
                                'label' => 'View Report',
                                'url'   => route('admin.forms.grievances.view', $grievance->grievance_ticket_id),
                                'open_new_tab' => true,
                            ],
                        ]
                    ));
                }

                $user->notify(new GeneralNotification(
                    'Reroute Successful',
                    "You have rerouted the report '{$grievance->grievance_title}' to {$department->department_name}.",
                    'success',
                    ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                    ['type' => 'success'],
                    true,
                    []
                ));
            }
        });

        $this->dispatch('reroute-success');

        $this->resetPage();
        $this->updateStats();
        $this->resetInputFields();
    }

    public function updateSelectedGrievanceStatus(): void
    {
        $this->validate([
            'selected' => 'required|array|min:1',
            'status'   => 'required|string',
        ], [
            'selected.required' => 'Please select at least one grievance to update.',
            'status.required'   => 'Please choose a grievance status.',
        ]);

        $user = auth()->user();
        $formattedStatus = $this->formatStatus($this->status);

        DB::beginTransaction();

        try {
            foreach ($this->selected as $grievanceId) {

                $grievance = Grievance::find($grievanceId);
                if (!$grievance) continue;

                $oldStatus = $grievance->grievance_status;

                $grievance->update([
                    'grievance_status' => $formattedStatus,
                    'updated_at'       => now(),
                ]);

                $grievance->addRemark([
                    'message'   => "Status changed from '{$this->displayText($oldStatus)}' to '{$this->displayText($formattedStatus)}' by {$user->name} (" . $this->displayRoleName($user->getRoleNames()->first()) .").",
                    'user_id'   => $user->id,
                    'user_name' => $user->name,
                    'role'      => $this->displayRoleName($user->getRoleNames()->first()),
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                    'status'    => $formattedStatus,
                    'type'      => 'status_update',
                ]);

                ActivityLog::create([
                    'user_id'      => $user->id,
                    'role_id'      => $user->roles->first()?->id,
                    'module'       => 'Report Management',
                    'action'       => "Changed report #{$grievance->grievance_id} status from {$oldStatus} to {$formattedStatus}",
                    'action_type'  => 'update_status',
                    'model_type'   => 'App\\Models\\Grievance',
                    'model_id'     => $grievance->grievance_id,
                    'description'  => "HR Liaison ({$user->email}) changed status of report #{$grievance->grievance_id} from {$oldStatus} to {$formattedStatus}.",
                    'status'       => 'success',
                    'ip_address'   => request()->ip(),
                    'device_info'  => request()->header('User-Agent'),
                    'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
                    'platform'     => php_uname('s'),
                    'timestamp'    => now(),
                ]);

                $ticketId = $grievance->grievance_ticket_id;

                $citizen = $grievance->user()->first();
                if ($citizen) {
                    $citizen->notify(new GeneralNotification(
                        'Report Status Updated',
                        "The status of your report '{$grievance->grievance_title}' has changed from '{$this->displayText($oldStatus)}' to '{$this->displayText($formattedStatus)}'.",
                        'info',
                        ['grievance_ticket_id' => $ticketId],
                        ['type' => 'info'],
                        true,
                        [[
                            'label' => 'View Report',
                            'url'   => route('citizen.grievance.view', $ticketId),
                            'open_new_tab' => false,
                        ]]
                    ));

                $feedbackUrl = URL::temporarySignedRoute(
                    'citizen.feedback-form',
                    now()->addDays(7),
                    ['ticket' => $ticketId]
                );

                if($formattedStatus === 'resolved'){
                    $citizen->notify(new GeneralNotification(
                            'Your Report Has Been Resolved',
                            "Your report '{$grievance->grievance_title}' has been successfully resolved. Please take a moment to submit your feedback.",
                            'success',
                            ['grievance_ticket_id' => $ticketId],
                            ['type' => 'success'],
                            true,
                            [
                                [
                                    'label'        => 'Submit Feedback',
                                    'url'          => $feedbackUrl,
                                    'open_new_tab' => false,
                                ],
                            ]
                        ));
                    }
                }

                $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
                    ->where('id', '!=', $user->id)
                    ->get();

                foreach ($admins as $admin) {
                    $admin->notify(new GeneralNotification(
                        'Report Status Updated',
                        "The status of report '{$grievance->grievance_title}' has changed from '{$this->displayText($oldStatus)}' to '{$this->displayText($formattedStatus)}'.",
                        'info',
                        ['grievance_ticket_id' => $ticketId],
                        ['type' => 'info'],
                        true,
                        [[
                            'label' => 'View Report',
                            'url'   => route('admin.forms.grievances.view', $ticketId),
                            'open_new_tab' => false,
                        ]]
                    ));
                }

                $user->notify(new GeneralNotification(
                    'Status Update Successful',
                    "You changed the status of '{$grievance->grievance_title}' from '{$this->displayText($oldStatus)}' to '{$this->displayText($formattedStatus)}'.",
                    'success',
                    ['grievance_ticket_id' => $ticketId],
                    ['type' => 'success'],
                    true,
                    [[
                        'label' => 'View Report',
                        'url'   => route('hr-liaison.grievance.view', $ticketId),
                        'open_new_tab' => false,
                    ]]
                ));
            }

            DB::commit();

            $this->dispatch('status-update-success');
            $this->resetPage();
            $this->updateStats();
            $this->resetInputFields();

        } catch (\Throwable $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Status Update Failed',
                'message' => "An error occurred while updating report statuses: {$e->getMessage()}.",
            ]);
        }
    }

    public function updateSelectedPriority(): void
    {
        $this->validate([
            'selected' => 'required|array|min:1',
            'priorityUpdate' => 'required|string',
        ], [
            'selected.required' => 'Please select at least one report to update.',
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

                    $grievance->addRemark([
                        'message'   => "Priority changed from '{$oldPriority}' to '{$formattedPriority}' by {$user->name} (" . $this->displayRoleName($user->getRoleNames()->first()) ."). Processing days updated from {$oldProcessingDays} to {$priorityProcessingDays}.",
                        'user_id'   => $user->id,
                        'user_name' => $user->name,
                        'role'      => $this->displayRoleName($user->getRoleNames()->first()),
                        'timestamp' => now()->format('Y-m-d H:i:s'),
                        'status'    => $grievance->grievance_status,
                        'type'      => 'priority_update',
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

                    $ticketId = $grievance->grievance_ticket_id;

                    $citizen = $grievance->user()->first();
                    if ($citizen) {
                        $citizen->notify(new GeneralNotification(
                            'Report Priority Updated',
                            "The priority of your report '{$grievance->grievance_title}' has changed from '{$this->displayText($oldPriority)}' to '{$this->displayText($formattedPriority)}'. Processing days updated from {$oldProcessingDays} to {$priorityProcessingDays}.",
                            'info',
                            ['grievance_ticket_id' => $ticketId],
                            ['type' => 'info'],
                            true,
                            [
                                [
                                    'label' => 'View Report',
                                    'url' => route('citizen.grievance.view', $ticketId),
                                    'open_new_tab' => false,
                                ]
                            ]
                        ));
                    }

                    $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new GeneralNotification(
                            'Report Priority Updated',
                            "The priority of report '{$grievance->grievance_title}' has changed from '{$this->displayText($oldPriority)}' to '{$this->displayText($formattedPriority)}'.",
                            'warning',
                            ['grievance_ticket_id' => $ticketId],
                            ['type' => 'warning'],
                            true,
                            [
                                [
                                    'label' => 'View Report',
                                    'url' => route('admin.forms.grievances.view', $ticketId),
                                    'open_new_tab' => false,
                                ]
                            ]
                        ));
                    }

                    $user->notify(new GeneralNotification(
                        'Priority Update Successful',
                        "You changed the priority of '{$grievance->grievance_title}' from '{$this->displayText($oldPriority)}' to '{$this->displayText($formattedPriority)}'.",
                        'success',
                        ['grievance_ticket_id' => $ticketId],
                        ['type' => 'success'],
                        true,
                        [
                            [
                                'label' => 'View Report',
                                'url' => route('hr-liaison.grievance.view', $ticketId),
                                'open_new_tab' => false,
                            ]
                        ]
                    ));
                }
            }

            DB::commit();

            $this->dispatch('priority-update-success');

            $this->resetPage();
            $this->updateStats();
            $this->resetInputFields();

        } catch (\Throwable $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Priority Update Failed',
                'message' => "An error occurred while updating report priorities: {$e->getMessage()}.",
            ]);
        }
    }

    public function downloadSelectedGrievancesPdf()
    {
        if (empty($this->selected)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Selected',
                'message' => "Please select at least one report to download.",
            ]);
            return;
        }

        $user = auth()->user();

        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->whereIn('grievance_id', $this->selected)
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => "The selected reports were not found or are not assigned to you.",
            ]);
            return;
        }

        $html = view('pdf.selected-grievances', [
            'grievances' => $grievances,
            'hr_liaison' => $user,
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
        $user = auth()->user();

        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => 'There are no reports assigned to you to download.',
            ]);
            return;
        }

        $html = view('pdf.all-grievances', [
            'grievances' => $grievances,
            'hr_liaison' => $user,
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

    public function printAllGrievances()
    {
        $user = auth()->user();

        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => 'There are no reports assigned to you to print.',
            ]);
            return;
        }

        return redirect()->route('print-all-grievances', ['grievances' => $grievances, 'hr_liaison' => $user]);

    }

    public function downloadReportsCsv()
    {
        $user = auth()->user();
        $userNameSlug = str_replace(' ', '_', $user->name);

        $reports = Grievance::with(['user.info', 'departments'])
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($reports->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reports Found',
                'message' => 'There are no reports assigned to you to export.',
            ]);
            return;
        }

        $filename = "reports-{$userNameSlug}-" . now()->format('Y_m_d_His') . ".csv";
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
                        $remarksStr .= '[' . ($r['timestamp'] ?? '') . '] ' . ($r['user_name'] ?? '—') .
                            ' (' . ($r['role'] ?? '—') . '): ' . ($r['message'] ?? '') . "\n";
                    }
                } else {
                    $remarksStr = '—';
                }

                fputcsv($handle, [
                    $report->grievance_ticket_id,
                    $report->grievance_title,
                    $report->grievance_type,
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

    public function downloadReportsRoutesCsv()
    {
        $user = auth()->user();
        $departmentIds = $user->departments->pluck('department_id');

        $reroutes = GrievanceReroute::with(['grievance', 'fromDepartment', 'toDepartment', 'performedBy'])
            ->whereIn('from_department_id', $departmentIds)
            ->orWhereIn('to_department_id', $departmentIds)
            ->latest()
            ->get();

        if ($reroutes->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reroutes Found',
                'message' => 'There are no reroutes assigned to your departments to export.',
            ]);
            return;
        }

        $filename = "reroutes-{$user->id}-" . now()->format('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($reroutes) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Reroute ID',
                'Ticket ID',
                'Status',
                'From Department',
                'To Department',
                'Performed By',
                'From Category',
                'To Category',
                'Date',
            ]);

            foreach ($reroutes as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->grievance->grievance_ticket_id ?? '—',
                    ucwords(str_replace('_', ' ', $r->grievance->grievance_status)) ?? '—',
                    $r->fromDepartment->department_name ?? '—',
                    $r->toDepartment->department_name ?? '—',
                    $r->performedBy->name ?? '—',
                    $r->from_category ?? '—',
                    $r->to_category ?? '—',
                    $r->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadReportsRoutesExcel()
    {
        $user = auth()->user();
        $userNameSlug = str_replace(' ', '_', $user->name);

        $departmentIds = $user->departments->pluck('department_id');

        $reroutes = GrievanceReroute::with(['grievance', 'fromDepartment', 'toDepartment', 'performedBy'])
            ->whereIn('from_department_id', $departmentIds)
            ->orWhereIn('to_department_id', $departmentIds)
            ->latest()
            ->get();

        if ($reroutes->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Reroutes Found',
                'message' => 'There are no reroutes assigned to your departments to export.',
            ]);
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reroutes');

        $headers = [
            'Reroute ID', 'Ticket ID', 'Status', 'From Department', 'To Department',
            'Performed By', 'From Category', 'To Category', 'Date'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . '1', $header);
        }

        $rowNumber = 2;
        foreach ($reroutes as $r) {
            $values = [
                $r->id,
                $r->grievance->grievance_ticket_id ?? '—',
                ucwords(str_replace('_', ' ', $r->grievance->grievance_status)) ?? '—',
                $r->fromDepartment->department_name ?? '—',
                $r->toDepartment->department_name ?? '—',
                $r->performedBy->name ?? '—',
                $r->from_category ?? '—',
                $r->to_category ?? '—',
                $r->created_at->format('Y-m-d H:i:s')
            ];

            foreach ($values as $col => $value) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . $rowNumber, $value);
            }

            $rowNumber++;
        }

        $filename = "reroutes-{$userNameSlug}-" . now()->format('Y_m_d_His') . ".xlsx";
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
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

    public function applyRerouteFilters()
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
            $query->whereDate('created_at', $this->filterDate);
        }

        $this->totalGrievances     = $query->count();
        $this->criticalPriorityCount = (clone $query)->where('priority_level', 'Critical')->count();
        $this->highPriorityCount     = (clone $query)->where('priority_level', 'High')->count();
        $this->normalPriorityCount   = (clone $query)->where('priority_level', 'Normal')->count();
        $this->lowPriorityCount      = (clone $query)->where('priority_level', 'Low')->count();

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
                    'Overdue' => 'overdue',
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
            ->when($this->filterDate, function ($q) {
                $q->whereDate('created_at', $this->filterDate);
            })
            ->when($this->sortField, function($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            }, function($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($this->perPage, ['*'], 'grievancesPage');

            $departmentIds = $user->departments->pluck('department_id')->toArray();

            $grievanceReroutes = GrievanceReroute::with([
                    'grievance',
                    'fromDepartment',
                    'toDepartment',
                    'performedBy'
                ])
                ->where(function ($q) use ($departmentIds) {
                    $q->whereIn('from_department_id', $departmentIds)
                    ->orWhereIn('to_department_id', $departmentIds);
                })
                ->when($this->filterRerouteStatus && $this->filterRerouteStatus !== 'Show All', function ($q) {
                    $statusMap = [
                        'Pending' => 'pending',
                        'Acknowledged' => 'acknowledged',
                        'In Progress' => 'in_progress',
                        'Escalated' => 'escalated',
                        'Resolved' => 'resolved',
                        'Unresolved' => 'unresolved',
                        'Closed' => 'closed',
                        'Overdue' => 'overdue',
                    ];
                    if (isset($statusMap[$this->filterRerouteStatus])) {
                        $q->whereHas('grievance', fn($g) => $g->where('grievance_status', $statusMap[$this->filterRerouteStatus]));
                    }
                })
                ->when($this->filterFromDepartment, function ($q) {
                    $q->whereHas('fromDepartment', function ($sub) {
                        $sub->where('department_name', $this->filterFromDepartment);
                    });
                })
                ->when($this->filterToDepartment, function ($q) {
                    $q->whereHas('toDepartment', function ($sub) {
                        $sub->where('department_name', $this->filterToDepartment);
                    });
                })
                ->when($this->filterRerouteCategory, fn($q) => $q->where('from_category', $this->filterRerouteCategory)->orWhere('to_category', $this->filterRerouteCategory))
                ->when($this->searchReroutes, function ($q) {
                    $term = '%' . $this->searchReroutes . '%';
                    $q->where(function ($sub) use ($term) {
                        $sub->where('id', 'like', $term)
                            ->orWhere('from_category', 'like', $term)
                            ->orWhere('to_category', 'like', $term)
                            ->orWhereHas('grievance', function ($g) use ($term) {
                                $g->where('grievance_ticket_id', 'like', $term)
                                ->orWhere('grievance_title', 'like', $term);
                            })
                            ->orWhereHas('fromDepartment', fn ($d) =>
                                $d->where('department_name', 'like', $term)
                            )
                            ->orWhereHas('toDepartment', fn ($d) =>
                                $d->where('department_name', 'like', $term)
                            )
                            ->orWhereHas('performedBy', fn ($u) =>
                                $u->where('name', 'like', $term)
                            );
                    });
                })
                ->orderBy($this->rerouteSortField, $this->rerouteSortDirection)
                ->paginate($this->reroutePerPage, ['*'], 'reroutesPage');


        return view('livewire.user.hr-liaison.grievance.index', compact('grievances', 'grievanceReroutes'));
    }
}
