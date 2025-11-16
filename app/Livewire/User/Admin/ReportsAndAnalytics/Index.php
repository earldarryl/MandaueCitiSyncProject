<?php

namespace App\Livewire\User\Admin\ReportsAndAnalytics;

use App\Models\Department;
use Livewire\Component;
use App\Models\Grievance;
use App\Models\Feedback;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Reports & Analytics')]
class Index extends Component
{
    public $data = [];
    public $filterType = '';
    public bool $filtersApplied = false;
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
    public $dynamicFeedbackFilter;
    public $dynamicFeedbackStats = [];
    public $dynamicFeedbackFilterOptions;
    public $filterCCSummary;
    public $filterSQDSummary;
    public $filterUserType = 'Citizen';
    public $filterGender;
    public $filterBarangay;
    public $filterCivilStatus;
    public $dynamicUserFilter;
    public $dynamicUserFilterOptions;
    public $dynamicUserStats = [];
    public $barangayOptions = [];
    public $startDate;
    public $endDate;
    public $serviceOptions = [];
    public $dynamicColorMap = [
        'Average Age of Citizens' => ['bg' => 'from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800', 'text' => 'text-blue-600 dark:text-blue-400'],
        'Oldest Citizen'          => ['bg' => 'from-red-50 to-red-100 dark:from-red-900 dark:to-red-800',   'text' => 'text-red-600 dark:text-red-400'],
        'Youngest Citizen'        => ['bg' => 'from-green-50 to-green-100 dark:from-green-900 dark:to-green-800', 'text' => 'text-green-600 dark:text-green-400'],
        'Barangay with Most Grievances' => ['bg' => 'from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800', 'text' => 'text-orange-600 dark:text-orange-400'],
        'Most Registered Barangay' => ['bg' => 'from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800', 'text' => 'text-purple-600 dark:text-purple-400'],
        'Gender Distribution' => ['bg' => 'from-pink-50 to-pink-100 dark:from-pink-900 dark:to-pink-800', 'text' => 'text-pink-600 dark:text-pink-400'],

        'High → Low Priority' => ['bg' => 'from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800', 'text' => 'text-yellow-600 dark:text-yellow-300'],
        'Most Submitted Grievance Type' => ['bg' => 'from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800', 'text' => 'text-blue-600 dark:text-blue-400'],
        'Status Counts' => ['bg' => 'from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800', 'text' => 'text-gray-600 dark:text-gray-300'],

        'Most Assignments' => ['bg' => 'from-indigo-50 to-indigo-100 dark:from-indigo-900 dark:to-indigo-800', 'text' => 'text-indigo-600 dark:text-indigo-400'],
        'Most Active & Available' => ['bg' => 'from-teal-50 to-teal-100 dark:from-teal-900 dark:to-teal-800', 'text' => 'text-teal-600 dark:text-teal-400'],

        'Awareness (Highest → Lowest)' => ['bg' => 'from-cyan-50 to-cyan-100 dark:from-cyan-900 dark:to-cyan-800', 'text' => 'text-cyan-600 dark:text-cyan-400'],
        'Satisfaction (Most Agree → Most Disagree)' => ['bg' => 'from-lime-50 to-lime-100 dark:from-lime-900 dark:to-lime-800', 'text' => 'text-lime-600 dark:text-lime-400'],
    ];

    public $grievanceColorMap = [
        'High' => ['bg' => 'from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800', 'text' => 'text-yellow-600 dark:text-yellow-300'],
        'Normal' => ['bg' => 'from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800', 'text' => 'text-orange-600 dark:text-orange-400'],
        'Low' => ['bg' => 'from-green-50 to-green-100 dark:from-green-900 dark:to-green-800', 'text' => 'text-green-600 dark:text-green-400'],

        'Complaint' => ['bg' => 'from-red-50 to-red-100 dark:from-red-900 dark:to-red-800', 'text' => 'text-red-600 dark:text-red-400'],
        'Request' => ['bg' => 'from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800', 'text' => 'text-blue-600 dark:text-blue-400'],
        'Inquiry' => ['bg' => 'from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800', 'text' => 'text-purple-600 dark:text-purple-400'],

        'Pending' => ['bg' => 'from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800', 'text' => 'text-gray-600 dark:text-gray-300'],
        'Delayed' => ['bg' => 'from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800', 'text' => 'text-orange-600 dark:text-orange-400'],
        'Acknowledged' => ['bg' => 'from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800', 'text' => 'text-blue-600 dark:text-blue-400'],
        'In Progress' => ['bg' => 'from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800', 'text' => 'text-yellow-600 dark:text-yellow-300'],
        'Escalated' => ['bg' => 'from-red-50 to-red-100 dark:from-red-900 dark:to-red-800', 'text' => 'text-red-600 dark:text-red-400'],
        'Resolved' => ['bg' => 'from-green-50 to-green-100 dark:from-green-900 dark:to-green-800', 'text' => 'text-green-600 dark:text-green-400'],
        'Unresolved' => ['bg' => 'from-pink-50 to-pink-100 dark:from-pink-900 dark:to-pink-800', 'text' => 'text-pink-600 dark:text-pink-400'],
        'Closed' => ['bg' => 'from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800', 'text' => 'text-gray-600 dark:text-gray-300'],
    ];

    public function getDynamicTitle(): string
    {
        $type = $this->filterType ?: 'All Types';

        $type = ucwords($type);

        if ($type === 'All Types') {
            return 'Report based on All Types';
        } elseif ($type !== 'All Types') {
            return "Report about {$type}";
        } elseif ($type !== 'All Types') {
            return "Report about {$type}";
        } else {
            return "Report based on All Types";
        }
    }
    private function applyColorMap($stats)
    {
        return collect($stats)->map(function ($stat) {
            $key = $stat->label
                ?? $stat->priority_level
                ?? $stat->grievance_type
                ?? $stat->grievance_status
                ?? null;

            $color = $this->grievanceColorMap[$key]
                ?? $this->dynamicColorMap[$key]
                ?? [
                    'bg' => 'from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700',
                    'text' => 'text-gray-600 dark:text-gray-300'
                ];

            $stat->bg = $color['bg'];
            $stat->text = $color['text'];

            return $stat;
        });
    }

    private function formatDynamicStatsForExport($stats)
    {
        return collect($stats)->map(function ($stat) {
            return [
                'label' => $stat->label ?? $stat->department_name ?? $stat->grievance_type ?? $stat->priority_level ?? '-',
                'total' => $stat->total ?? '-',
                'extra' => $stat->total_online_time ?? ($stat->percentage ?? '-') ?? '-',
            ];
        });
    }

    public function mount()
    {
        $this->serviceOptions = Feedback::select('service')
            ->distinct()
            ->pluck('service')
            ->toArray();

        $this->barangayOptions = User::with('userInfo')
            ->whereHas('userInfo', fn($q) => $q->whereNotNull('barangay'))
            ->get()
            ->pluck('userInfo.barangay')
            ->unique()
            ->values()
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

        $this->dynamicFeedbackFilterOptions = [
            'Awareness (Highest → Lowest)' => 'Awareness (Highest → Lowest)',
            'Satisfaction (Most Agree → Most Disagree)' => 'Satisfaction (Most Agree → Most Disagree)',
        ];

        $this->dynamicUserFilterOptions = [
            'Average Age of Citizens' => 'Average Age of Citizens',
            'Oldest Citizen' => 'Oldest Citizen',
            'Youngest Citizen' => 'Youngest Citizen',
            'Barangay with Most Grievances' => 'Barangay with Most Grievances',
            'Most Registered Barangay' => 'Most Registered Barangay',
            'Gender Distribution' => 'Gender Distribution',
        ];

    }

    public function applyFilters()
    {
        $this->filtersApplied = true;
    }

    public function exportPDF()
    {
        $this->data = collect();
        $stats =
            $this->filterType === 'Departments' ? $this->dynamicDepartmentStats :
            ($this->filterType === 'Feedbacks' ? $this->dynamicFeedbackStats :
            ($this->filterType === 'Users' ? $this->dynamicUserStats :
            $this->dynamicGrievanceStats));

        switch ($this->filterType) {
            case 'Grievances':
                $this->data = Grievance::with(['user', 'departments'])
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                    ->when($this->grievanceType, fn($q) => $q->where('grievance_type', $this->grievanceType))
                    ->when($this->grievancePriority, fn($q) => $q->where('priority_level', $this->grievancePriority))
                    ->when($this->grievanceStatus, fn($q) => $q->where('grievance_status', $this->grievanceStatus))
                    ->latest()
                    ->get();
                break;

            case 'Departments':
                $this->data = Department::with('hrLiaisons')
                    ->when($this->filterServiceStatus, fn($q) => $q->where('is_active', $this->filterServiceStatus === 'Active'))
                    ->when($this->filterServiceAvailability, fn($q) => $q->where('is_available', $this->filterServiceAvailability === 'Available'))
                    ->latest()
                    ->get();
                break;

            case 'Feedbacks':
                $this->data = Feedback::with('user')
                    ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
                    ->when($this->filterGender, fn($q) => $q->where('gender', $this->filterGender))
                    ->latest()
                    ->get();
                break;

            case 'Users':
                $this->data = User::with(['roles', 'userInfo'])
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                    ->latest()
                    ->get();
                break;
        }

        $html = view('pdf.admin-report', [
        'data' => $this->data,
        'stats' => $this->applyColorMap($stats),
        'filterType' => $this->filterType,
        'filterCategory' => $this->filterCategory ?? 'All Categories',
        'startDate' => $this->startDate,
        'endDate' => $this->endDate,
        'adminName' => Auth::user()->name,
        'dynamicTitle' => $this->getDynamicTitle(),
    ])->render();


        $pdfPath = storage_path('app/public/admin-report.pdf');

        Browsershot::html($html)
            ->setNodeBinary('C:\Program Files\nodejs\node.exe')
            ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->delay(6000)
            ->timeout(120)
            ->format('A4')
            ->save($pdfPath);

        return response()->download($pdfPath, 'admin-report.pdf');
    }

    public function printReport()
    {
        $stats =
            $this->filterType === 'Departments' ? $this->dynamicDepartmentStats :
            ($this->filterType === 'Feedbacks' ? $this->dynamicFeedbackStats :
            ($this->filterType === 'Users' ? $this->dynamicUserStats :
            $this->dynamicGrievanceStats));

        $mappedStats = collect($stats)->map(function ($stat) {
            return (object)[
                'label' => $stat->label ?? $stat->department_name ?? $stat->grievance_type ?? $stat->priority_level ?? 'N/A',
                'total' => $stat->total ?? 0,
                'extra' => $stat->extra ?? $stat->percentage ?? 0,
            ];
        });

        $mappedStats = $this->applyColorMap($mappedStats);

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
                $data = Department::with('hrLiaisons')
                    ->when($this->filterServiceStatus, fn($q) => $q->where('is_active', $this->filterServiceStatus === 'Active'))
                    ->when($this->filterServiceAvailability, fn($q) => $q->where('is_available', $this->filterServiceAvailability === 'Available'))
                    ->latest()
                    ->get();
                break;

            case 'Feedbacks':
                $data = Feedback::with('user')
                    ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
                    ->when($this->filterGender, fn($q) => $q->where('gender', $this->filterGender))
                    ->latest()
                    ->get();
                break;

            case 'Users':
                $data = User::with(['roles', 'userInfo'])
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                    ->latest()
                    ->get();
                break;

            default:
                $data = collect();
        }

        $cacheKey = 'admin-report-' . auth()->id() . '-' . now()->timestamp;

        cache()->put($cacheKey, [
            'data' => $data,
            'stats' => $mappedStats,
            'filterType' => $this->filterType,
            'filterCategory' => $this->filterCategory ?? 'All Categories',
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'adminName' => auth()->user()->name,
            'dynamicTitle' => $this->getDynamicTitle(),
        ], now()->addMinutes(10));

        return redirect()->route('print-admin-reports', ['key' => $cacheKey]);
    }

    public function exportCSV()
    {
        $filename = strtolower($this->filterType ?? 'report') . '-' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');
        $user = Auth::user();

        $formattedStart = $this->startDate ? \Carbon\Carbon::parse($this->startDate)->format('F d, Y') : 'N/A';
        $formattedEnd = $this->endDate ? \Carbon\Carbon::parse($this->endDate)->format('F d, Y') : 'N/A';

        $stats =
            $this->filterType === 'Departments' ? $this->dynamicDepartmentStats :
            ($this->filterType === 'Feedbacks' ? $this->dynamicFeedbackStats :
            ($this->filterType === 'Users' ? $this->dynamicUserStats :
            $this->dynamicGrievanceStats));

        $statsForExport = $this->formatDynamicStatsForExport($stats);

        fputcsv($handle, ["Admin Report - {$this->filterType}"]);
        fputcsv($handle, ["Generated By:", $user->name]);
        fputcsv($handle, ["Date Range:", "$formattedStart – $formattedEnd"]);
        fputcsv($handle, ["Exported At:", now()->format('F d, Y — h:i A')]);
        fputcsv($handle, []);

        fputcsv($handle, ["Dynamic Stats"]);
        fputcsv($handle, ["Label", "Value", "Extra"]);

        foreach ($statsForExport as $stat) {
            fputcsv($handle, [$stat['label'], $stat['total'], $stat['extra']]);
        }

        fputcsv($handle, []);

        switch ($this->filterType) {
            case 'Grievances':
                fputcsv($handle, ['Ticket ID', 'Title', 'Type', 'Category', 'Status', 'Processing Days', 'Date & Time']);
                foreach ($this->data as $item) {
                    fputcsv($handle, [
                        $item->grievance_ticket_id,
                        $item->grievance_title,
                        $item->grievance_type ?? '-',
                        $item->grievance_category ?? '-',
                        $item->grievance_status ?? '-',
                        $item->processing_days ?? '—',
                        $item->created_at?->format('F d, Y — h:i A') ?? '-',
                    ]);
                }
                break;

            case 'Departments':
                fputcsv($handle, ['Department', 'Assignments Count', 'Active Liaisons', 'Available', 'Status']);
                foreach ($this->data as $item) {
                    fputcsv($handle, [
                        $item->department_name,
                        $item->assignments_count ?? 0,
                        $item->hrLiaisons->count() ?? 0,
                        $item->is_available ? 'Yes' : 'No',
                        $item->is_active ? 'Active' : 'Inactive',
                    ]);
                }
                break;

            case 'Feedbacks':
                fputcsv($handle, ['User', 'Service', 'Gender', 'Date', 'Awareness', 'Satisfaction']);
                foreach ($this->data as $item) {
                    fputcsv($handle, [
                        $item->user->name ?? '-',
                        $item->service ?? '-',
                        $item->gender ?? '-',
                        $item->date?->format('F d, Y') ?? '-',
                        $item->cc_summary ?? '-',
                        $item->sqd_summary ?? '-',
                    ]);
                }
                break;

            case 'Users':
                fputcsv($handle, ['Name', 'Email', 'Roles', 'Gender', 'Barangay', 'Civil Status', 'Registered At']);
                foreach ($this->data as $item) {
                    fputcsv($handle, [
                        $item->name ?? '-',
                        $item->email ?? '-',
                        $item->roles ?? '-',
                        $item->userInfo->gender ?? '-',
                        $item->userInfo->barangay ?? '-',
                        $item->userInfo->civil_status ?? '-',
                        $item->created_at?->format('F d, Y') ?? '-',
                    ]);
                }
                break;
        }

        rewind($handle);
        return Response::streamDownload(fn() => print(stream_get_contents($handle)), $filename);
    }

    public function exportExcel()
    {
        $user = Auth::user();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $val = fn($v) => ($v !== null && $v !== '') ? $v : 'N/A';

        $stats =
            $this->filterType === 'Departments' ? $this->dynamicDepartmentStats :
            ($this->filterType === 'Feedbacks' ? $this->dynamicFeedbackStats :
            ($this->filterType === 'Users' ? $this->dynamicUserStats :
            $this->dynamicGrievanceStats));

        $statsForExport = $this->formatDynamicStatsForExport($stats);

        $sheet->fromArray([
            ["Admin Report - " . $val($this->filterType)],
            ["Report Generated By:", $val($user->name)],
            ["Date Range:",
                ($this->startDate ? \Carbon\Carbon::parse($this->startDate)->format('F d, Y') : 'N/A')
                . ' – ' .
                ($this->endDate ? \Carbon\Carbon::parse($this->endDate)->format('F d, Y') : 'N/A')
            ],
            ["Exported At:", now()->format('F d, Y — h:i A')],
            [],
            ["Dynamic Stats"],
            ["Label", "Value", "Extra"],
        ], null, 'A1');

        $rowNum = 8;

        foreach ($statsForExport as $stat) {
            $sheet->fromArray([
                $val($stat['label']),
                $val($stat['total']),
                $val($stat['extra'])
            ], null, "A{$rowNum}");
            $rowNum++;
        }

        $rowNum += 2;

        switch ($this->filterType) {
            case 'Grievances':
                $headers = ['Ticket ID', 'Title', 'Type', 'Category', 'Status', 'Processing Days', 'Date & Time'];
                break;
            case 'Departments':
                $headers = ['Department', 'Assignments Count', 'Active Liaisons', 'Available', 'Status'];
                break;
            case 'Feedbacks':
                $headers = ['User', 'Service', 'Gender', 'Date', 'Awareness', 'Satisfaction'];
                break;
            case 'Users':
                $headers = ['Name', 'Email', 'Roles', 'Gender', 'Barangay', 'Civil Status', 'Registered At'];
                break;
            default:
                $headers = [];
        }

        $sheet->fromArray($headers, null, "A{$rowNum}");
        $rowNum++;

        foreach ($this->data as $item) {

            switch ($this->filterType) {
                case 'Grievances':
                    $row = [
                        $val($item->grievance_ticket_id),
                        $val($item->grievance_title),
                        $val($item->grievance_type),
                        $val($item->grievance_category),
                        $val($item->grievance_status),
                        $val($item->processing_days),
                        $val($item->created_at?->format('F d, Y — h:i A')),
                    ];
                    break;

                case 'Departments':
                    $row = [
                        $val($item->department_name),
                        $val($item->assignments_count),
                        $val($item->hrLiaisons->count()),
                        $item->is_available ? 'Yes' : 'No',
                        $item->is_active ? 'Active' : 'Inactive',
                    ];
                    break;

                case 'Feedbacks':
                    $row = [
                        $val($item->user->name ?? null),
                        $val($item->service),
                        $val($item->gender),
                        $val($item->date?->format('F d, Y')),
                        $val($item->cc_summary),
                        $val($item->sqd_summary),
                    ];
                    break;

                case 'Users':
                    $row = [
                        $val($item->name),
                        $val($item->email),
                        $val($item->roles),
                        $val($item->userInfo->gender ?? null),
                        $val($item->userInfo->barangay ?? null),
                        $val($item->userInfo->civil_status ?? null),
                        $val($item->created_at?->format('F d, Y')),
                    ];
                    break;
            }

            $sheet->fromArray($row, null, "A{$rowNum}");
            $rowNum++;
        }

        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = strtolower($this->filterType ?? 'report') . '-' . now()->format('Y-m-d_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }


    public function render()
    {
        $this->data = collect();

        switch ($this->filterType) {
            case 'Grievances':
                $this->data = Grievance::with(['user', 'departments'])
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                    ->when($this->grievanceType, fn($q) => $q->where('grievance_type', $this->grievanceType))
                    ->when($this->grievancePriority, fn($q) => $q->where('priority_level', $this->grievancePriority))
                    ->when($this->grievanceStatus, fn($q) => $q->where('grievance_status', $this->grievanceStatus))
                    ->latest()
                    ->get();
                break;

            case 'Departments':
                $this->data = Department::withCount('assignments')
                    ->when($this->filterServiceStatus, fn($q) => $q->where('is_active', $this->filterServiceStatus === 'Active'))
                    ->when($this->filterServiceAvailability, fn($q) => $q->where('is_available', $this->filterServiceAvailability === 'Available'))
                    ->with('hrLiaisons')
                    ->latest()
                    ->get();
                break;

            case 'Feedbacks':
                $this->data = Feedback::with('user')
                    ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))

                    ->when($this->filterGender, fn($q) => $q->where('gender', $this->filterGender))
                    ->when($this->filterCCSummary, fn($q) => $q->where('cc_summary', $this->filterCCSummary))
                    ->when($this->filterSQDSummary, fn($q) => $q->where('sqd_summary', $this->filterSQDSummary))

                    ->latest()
                    ->get();
                break;

            case 'Users':
                $this->data = User::with(['roles', 'userInfo'])
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))

                    ->when($this->filterUserType === 'Citizen', fn($q) =>
                        $q->whereHas('roles', fn($r) => $r->where('name', 'citizen'))
                    )
                    ->when($this->filterUserType === 'HR Liaison', fn($q) =>
                        $q->whereHas('roles', fn($r) => $r->where('name', 'hr_liaison'))
                    )

                    ->when($this->filterGender, fn($q) =>
                        $q->whereHas('userInfo', fn($ui) => $ui->where('gender', $this->filterGender))
                    )
                    ->when($this->filterBarangay, fn($q) =>
                        $q->whereHas('userInfo', fn($ui) => $ui->where('barangay', $this->filterBarangay))
                    )
                    ->when($this->filterCivilStatus, fn($q) =>
                        $q->whereHas('userInfo', fn($ui) => $ui->where('civil_status', $this->filterCivilStatus))
                    )

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
                $this->dynamicDepartmentStats = $this->data
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
                $this->dynamicDepartmentStats = $this->data
                    ->filter(fn($dept) => $dept->is_active && $dept->is_available)
                    ->sortByDesc(fn($dept) => $dept->hrLiaisons->filter(fn($user) => $user->isOnline())->count())
                    ->take(5)
                    ->map(function ($dept) {
                        $activeLiaisons = $dept->hrLiaisons->filter(fn($user) => $user->isOnline());
                        $totalLiaisons = $dept->hrLiaisons->count();

                        $totalOnlineMinutes = $activeLiaisons->sum(function ($user) {
                            return $user->last_seen_at
                                ? now()->diffInMinutes($user->last_seen_at)
                                : 0;
                        });

                        $hours = floor($totalOnlineMinutes / 60);
                        $minutes = $totalOnlineMinutes % 60;
                        $totalOnlineFormatted = "{$hours}h {$minutes}m";

                        return (object)[
                            'department_name' => $dept->department_name,
                            'total' => $activeLiaisons->count() . " / " . $totalLiaisons,
                            'icon' => 'heroicon-o-users',
                            'total_online_time' => $totalOnlineFormatted,
                        ];
                    })->values();
            }

        }


        $this->dynamicFeedbackStats = [];

        if ($this->filterType === 'Feedbacks' && $this->dynamicFeedbackFilter) {

            $baseQuery = Feedback::query()
                ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate));

            if ($this->dynamicFeedbackFilter === 'Awareness (Highest → Lowest)') {

                $awarenessLevels = [
                    'High Awareness',
                    'Medium Awareness',
                    'Low Awareness',
                    'No Awareness',
                    'N/A',
                ];

                $raw = $baseQuery
                    ->selectRaw("cc_summary, COUNT(*) as total")
                    ->groupBy('cc_summary')
                    ->pluck('total', 'cc_summary');

                $this->dynamicFeedbackStats = collect($awarenessLevels)->map(function ($label) use ($raw) {
                    return (object)[
                        'label' => $label,
                        'total' => $raw[$label] ?? 0,
                        'icon' => 'heroicon-o-light-bulb',
                    ];
                });
            }

            if ($this->dynamicFeedbackFilter === 'Satisfaction (Most Agree → Most Disagree)') {

                $satisfactionLevels = [
                    'Most Agree',
                    'Neutral',
                    'Most Disagree',
                    'N/A',
                ];

                $raw = $baseQuery
                    ->selectRaw("sqd_summary, COUNT(*) as total")
                    ->groupBy('sqd_summary')
                    ->pluck('total', 'sqd_summary');

                $this->dynamicFeedbackStats = collect($satisfactionLevels)->map(function ($label) use ($raw) {
                    return (object)[
                        'label' => $label,
                        'total' => $raw[$label] ?? 0,
                        'icon' => 'heroicon-o-face-smile',
                    ];
                });
            }
        }

        $this->dynamicUserStats = [];

        if ($this->filterType === 'Users' && $this->dynamicUserFilter) {

            $baseQuery = User::query()
                ->whereHas('roles', fn($q) => $q->where('name', 'citizen'))
                ->with('userInfo');

            if ($this->dynamicUserFilter === 'Average Age of Citizens') {
                $avgAge = $baseQuery->get()->avg(fn($u) => $u->userInfo->age ?? 0);

                $this->dynamicUserStats = collect([
                    (object)[
                        'label' => 'Average Age of Citizens',
                        'total' => round($avgAge, 1),
                        'icon' => 'heroicon-o-chart-bar',
                    ]
                ]);
            }

            if ($this->dynamicUserFilter === 'Oldest Citizen') {
                $oldest = $baseQuery->get()->max(fn($u) => $u->userInfo->age ?? 0);

                $this->dynamicUserStats = collect([
                    (object)[
                        'label' => 'Oldest Registered Citizen',
                        'total' => $oldest,
                        'icon' => 'heroicon-o-user',
                    ]
                ]);
            }

            if ($this->dynamicUserFilter === 'Youngest Citizen') {
                $youngest = $baseQuery->get()->min(fn($u) => $u->userInfo->age ?? 999);

                $this->dynamicUserStats = collect([
                    (object)[
                        'label' => 'Youngest Registered Citizen',
                        'total' => $youngest,
                        'icon' => 'heroicon-o-user-circle',
                    ]
                ]);
            }

            if ($this->dynamicUserFilter === 'Barangay with Most Grievances') {

                $barangay = Grievance::query()
                    ->join('users', 'users.id', '=', 'grievances.user_id')
                    ->join('user_infos', 'user_infos.user_id', '=', 'users.id')
                    ->selectRaw('user_infos.barangay as barangay, COUNT(*) as total')
                    ->groupBy('user_infos.barangay')
                    ->orderByDesc('total')
                    ->first();

                $this->dynamicUserStats = collect([
                    (object)[
                        'label' => $barangay->barangay ?? 'No Data',
                        'total' => $barangay->total ?? 0,
                        'icon' => 'heroicon-o-home',
                    ]
                ]);
            }

            if ($this->dynamicUserFilter === 'Most Registered Barangay') {
                $barangay = User::whereHas('roles', fn($r) => $r->where('name', 'citizen'))
                    ->join('user_infos', 'users.id', '=', 'user_infos.user_id')
                    ->selectRaw('user_infos.barangay, COUNT(*) as total')
                    ->groupBy('user_infos.barangay')
                    ->orderByDesc('total')
                    ->first();

                $this->dynamicUserStats = collect([
                    (object)[
                        'label' => $barangay->barangay ?? 'No Data',
                        'total' => $barangay->total ?? 0,
                        'icon' => 'heroicon-o-map-pin',
                    ]
                ]);
            }

            if ($this->dynamicUserFilter === 'Gender Distribution') {
                $genders = User::whereHas('roles', fn($r) => $r->where('name', 'citizen'))
                    ->join('user_infos', 'users.id', '=', 'user_infos.user_id')
                    ->selectRaw('gender, COUNT(*) as total')
                    ->groupBy('gender')
                    ->get()
                    ->map(fn($g) => (object)[
                        'label' => ucfirst($g->gender),
                        'total' => $g->total,
                        'icon' => 'heroicon-o-user-group',
                    ]);

                $this->dynamicUserStats = $genders;
            }
        }

        $stats =
            $this->filterType === 'Departments' ? $this->dynamicDepartmentStats :
            ($this->filterType === 'Feedbacks' ? $this->dynamicFeedbackStats :
            ($this->filterType === 'Users' ? $this->dynamicUserStats :
            $this->dynamicGrievanceStats));

        return view('livewire.user.admin.reports-and-analytics.index', [
            'data' => $this->data,
            'stats' => $this->applyColorMap($stats),
        ]);

    }
}
