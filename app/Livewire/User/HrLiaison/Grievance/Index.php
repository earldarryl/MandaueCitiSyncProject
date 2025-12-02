<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\Grievance;
use App\Models\GrievanceAttachment;
use App\Models\HistoryLog;
use App\Models\HrLiaisonDepartment;
use App\Models\User;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Spatie\Browsershot\Browsershot;
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
    public $priorityUpdate;
    public $departmentOptions;
    public $categoryOptions;
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

        $liaisonDepartments = $currentHrLiaison->departments->pluck('department_name')->toArray();

        $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->whereNotIn('department_name', $liaisonDepartments)
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
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($reports->isEmpty()) {
            Notification::make()
                ->title('No Reports Found')
                ->body('The selected reports were not found or are not assigned to you.')
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
            $attachments = $report->attachments->pluck('file_name')->join(', ') ?: 'N/A';

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
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($reports->isEmpty()) {
            Notification::make()
                ->title('No Reports Found')
                ->body('There are no reports assigned to you to export.')
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
            $attachments = $report->attachments->pluck('file_name')->join(', ') ?: 'N/A';

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

                $existingReport = Grievance::withTrashed()->where('grievance_ticket_id', $ticketId)->first();
                if ($existingReport) {
                    $hasAssignments = Assignment::where('grievance_id', $existingReport->grievance_id)->exists();
                    if ($hasAssignments) {
                        $skippedCount++;
                        continue;
                    }
                }

                $userId = $existingReport?->user_id ?? $currentUser->id;

                $processingDays = match (strtolower($priority)) {
                    'low'      => 20,
                    'normal'   => 7,
                    'high'     => 3,
                    'critical' => 1,
                    default    => 7,
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
                        'is_anonymous' => strtolower($submittedBy) === 'anonymous' ? 1 : 0,
                        'grievance_status' => strtolower($status) === 'pending' ? 'pending' : strtolower($status),
                        'processing_days' => $processingDays,
                    ]
                );

                $departmentNames = explode(',', $departments);
                foreach ($departmentNames as $deptName) {
                    $department = Department::where('department_name', trim($deptName))->first();
                    if ($department && $department->is_active && $department->is_available) {
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
                                'file_path' => 'grievance_files/' . $fileName,
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

            Notification::make()
                ->title('Import Completed')
                ->body('Reports have been imported successfully.' . ($skippedCount ? " Skipped {$skippedCount} duplicate(s)." : ""))
                ->success()
                ->send();

            Storage::disk('public')->delete($path);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Import Failed')
                ->body("Something went wrong while importing reports. Error: {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }


    public function printSelectedGrievances()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->title('No Reports Selected')
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
                ->title('No Reports Found')
                ->body('The selected reports were not found or are not assigned to you.')
                ->warning()
                ->send();
            return;
        }

        return redirect()->route('print-selected-grievances', [
            'selected' => implode(',', $this->selected),
        ]);
    }

    public function exportSelectedReportsCsv()
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

        $reports = Grievance::with(['user.info', 'departments'])
            ->whereIn('grievance_id', $this->selected)
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($reports->isEmpty()) {
            Notification::make()
                ->title('No Reports Found')
                ->body('The selected reports were not found or are not assigned to you.')
                ->warning()
                ->send();
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
                    Notification::make()
                        ->title('No HR Liaisons Found')
                        ->body("Department {$department->department_name} has no HR Liaisons assigned for report #{$grievance->grievance_ticket_id}.")
                        ->danger()
                        ->send();

                    continue;
                }

                $grievance->update([
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

        $this->redirectRoute('hr-liaison.grievance.index', navigate: true);
    }

    private function formatStatus($value)
    {
        return strtolower(str_replace(' ', '_', trim($value)));
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

                if ($grievance) {
                    $oldStatus = $grievance->grievance_status;

                    $grievance->update([
                        'grievance_status' => $formattedStatus,
                        'updated_at'       => now(),
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
                }
            }

            DB::commit();

            $this->dispatch('status-update-success');

            Notification::make()
                ->title('Report Status Updated')
                ->body(
                    'The selected reports have been updated to "' .
                    ucwords(str_replace('_', ' ', $formattedStatus)) . '".'
                )
                ->success()
                ->send();

            $this->redirectRoute('hr-liaison.grievance.index', navigate: true);

        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Status Update Failed')
                ->body('An error occurred while updating report statuses: ' . $e->getMessage())
                ->danger()
                ->send();
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
            'low'      => 7,
            'normal'   => 5,
            'high'     => 3,
            'critical' => 1,
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
                ->body('The selected reports have been updated to "' . ucfirst($formattedPriority) . '" with updated processing days.')
                ->success()
                ->send();

            $this->redirectRoute('hr-liaison.grievance.index', navigate: true);

        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Priority Update Failed')
                ->body('An error occurred while updating report priorities: ' . $e->getMessage())
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

        $user = auth()->user();

        $grievances = Grievance::with(['departments', 'user', 'attachments'])
            ->whereIn('grievance_id', $this->selected)
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->latest()
            ->get();

        if ($grievances->isEmpty()) {
            Notification::make()
                ->title('No Reports Found')
                ->body('The selected reports were not found or are not assigned to you.')
                ->warning()
                ->send();
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
            Notification::make()
                ->title('No Reports Found')
                ->body('There are no grievances assigned to you to download.')
                ->warning()
                ->send();
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
            Notification::make()
                ->title('No Reports Found')
                ->body('There are no grievances assigned to you to print.')
                ->warning()
                ->send();
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
            Notification::make()
                ->title('No Reports Found')
                ->body('There are no reports assigned to you to export.')
                ->warning()
                ->send();
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
            ->paginate($this->perPage);

        return view('livewire.user.hr-liaison.grievance.index', compact('grievances'));
    }
}
