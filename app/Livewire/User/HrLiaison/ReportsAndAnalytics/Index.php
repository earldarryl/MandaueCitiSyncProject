<?php

namespace App\Livewire\User\HrLiaison\ReportsAndAnalytics;

use Livewire\Component;
use App\Models\Grievance;
use Illuminate\Support\Facades\Auth;
use Spatie\Browsershot\Browsershot;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Reports & Analytics')]
class Index extends Component
{
    public $startDate;
    public $endDate;
    public $filterType = null;
    public $filterCategory = null;
    public $filtersApplied = false;
    public $sortOption = '';
    public $categoryOptions;
    public $category = 'all';
    public $categories = [];

    public $data = [];
    public $statuses;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');

        $this->categories = Grievance::query()
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', Auth::id()))
            ->distinct()
            ->pluck('grievance_category')
            ->toArray();

        $user = Auth::user();

        $departmentName = $user->departments->first()->department_name ?? null;

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

        $customCategories = Grievance::whereHas('assignments', function ($q) use ($user) {
                $departmentId = $user->departments->first()->department_id ?? null;
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

        $this->filterType = null;
        $this->filterCategory = null;

        $this->loadData();

    }

    public function applyFilters()
    {
        $this->filtersApplied = true;
        $this->loadData();
    }


    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadData();
    }

    public function loadData()
    {

        if (!$this->filtersApplied) {
            $this->data = [];
            $this->statuses = [];
            return;
        }

        $userId = Auth::id();

        $query = Grievance::query()
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $userId))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));

        if (!empty($this->filterType)) {
            $query->where('grievance_type', $this->filterType);
        }

        if (!empty($this->filterCategory)) {
            $query->where('grievance_category', $this->filterCategory);
        }

        if ($this->category !== 'all') {
            $query->where('grievance_category', $this->category);
        }

        switch ($this->sortOption) {

            case 'Priority: Low → Critical':
                $query->orderByRaw("
                    FIELD(priority_level, 'Low', 'Normal', 'High', 'Critical')
                ");
                break;

            case 'Priority: Critical → Low':
                $query->orderByRaw("
                    FIELD(priority_level, 'Critical', 'High', 'Normal', 'Low')
                ");
                break;


            case 'Type: Complaint → Request':
                $query->orderByRaw("
                    FIELD(grievance_type, 'Complaint', 'Inquiry', 'Request')
                ");
                break;

            case 'Type: Request → Complaint':
                $query->orderByRaw("
                    FIELD(grievance_type, 'Request', 'Inquiry', 'Complaint')
                ");
                break;


            case 'Status: Ascending':
                $query->orderBy('grievance_status', 'asc');
                break;

            case 'Status: Descending':
                $query->orderBy('grievance_status', 'desc');
                break;
        }

        $this->data = $query->orderBy($this->sortField, $this->sortDirection)->get();

        $grievances = Grievance::query()
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', Auth::id()))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->when(!empty($this->filterType), fn($q) => $q->where('grievance_type', $this->filterType))
            ->when(!empty($this->filterCategory), fn($q) => $q->where('grievance_category', $this->filterCategory))
            ->get();

        $statuses = [
            'Pending' => 0,
            'Overdue' => 0,
            'Resolved' => 0,
        ];

        foreach ($grievances as $grievance) {
            $status = strtolower($grievance->grievance_status ?? '');

            if ($status === 'resolved') {
                $statuses['Resolved']++;
            } elseif ($status === 'overdue') {
                $statuses['Overdue']++;
            } else {
                $statuses['Pending']++;
            }
        }

        $this->statuses = $statuses;

    }

    public function getDynamicTitle(): string
    {
        $type = $this->filterType ?: 'All Types';
        $category = $this->filterCategory ?: 'All Categories';

        $type = ucwords($type);
        $category = ucwords($category);

        if ($type === 'All Types' && $category === 'All Categories') {
            return 'Reports based on All Types in relation to All Categories';
        } elseif ($type !== 'All Types' && $category === 'All Categories') {
            return "Reports about {$type} in relation to All Categories";
        } elseif ($type !== 'All Types' && $category !== 'All Categories') {
            return "Reports about {$type} in relation to {$category}";
        } else {
            return "Reports based on All Types in relation to {$category}";
        }
    }

    public function exportPDF()
    {
        $html = view('pdf.mandauecitisync-report', [
            'data' => $this->data,
            'user' => Auth::user(),
            'statuses' => $this->statuses,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'filterType' => $this->filterType,
            'filterCategory' => $this->filterCategory,
            'hrName' => Auth::user()->name,
            'dynamicTitle' => $this->getDynamicTitle(),
        ])->render();

        $pdfPath = storage_path('app/public/mandauecitisync-report.pdf');


        Browsershot::html($html)
            ->setNodeBinary('C:\Program Files\nodejs\node.exe')
            ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->delay(2000)
            ->timeout(120)
            ->format('A4')
            ->save($pdfPath);

        return response()->download($pdfPath, 'mandauecitisync-report.pdf');
    }

    public function exportCSV()
    {
        $filename = 'mandauecitisync-report-' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        $user = Auth::user();

        $formattedStart = \Carbon\Carbon::parse($this->startDate)->format('F d, Y');
        $formattedEnd = \Carbon\Carbon::parse($this->endDate)->format('F d, Y');

        $type = $this->filterType ?? 'All Types';
        $category = $this->filterCategory ?? 'All Categories';
        $capitalize = fn($str) => ucwords(strtolower($str));
        $type = $capitalize($type);
        $category = $capitalize($category);

        if($type === 'All Types' && $category === 'All Categories'){
            $dynamicTitle = 'Reports based on All Types in relation to All Categories';
        } elseif($type !== 'All Types' && $category === 'All Categories'){
            $dynamicTitle = "Reports about $type in relation to All Categories";
        } elseif($type !== 'All Types' && $category !== 'All Categories'){
            $dynamicTitle = "Reports about $type in relation to $category";
        } else {
            $dynamicTitle = "Reports based on All Types in relation to $category";
        }

        fputcsv($handle, ["MandaueCitiSync Reports"]);
        fputcsv($handle, ["Report Generated By:", $user->name]);
        fputcsv($handle, ["Department:", $user->departments->pluck('department_name')->join(', ')]);
        fputcsv($handle, ["Role:", str_replace('Hr','HR',ucwords(str_replace('_',' ', $user->getRoleNames()->first() ?? 'N/A')))]);
        fputcsv($handle, ["Date Range:", "$formattedStart – $formattedEnd"]);
        fputcsv($handle, ["Exported At:", now()->format('F d, Y — h:i A')]);
        fputcsv($handle, ["Report Title:", $dynamicTitle]);
        fputcsv($handle, []);

        fputcsv($handle, ['Ticket ID', 'Title', 'Type', 'Category', 'Status', 'Processing Days', 'Date & Time']);

        foreach ($this->data as $item) {
            fputcsv($handle, [
                $item->grievance_ticket_id,
                $item->grievance_title,
                $item->grievance_type ?? '-',
                $item->grievance_category,
                $item->grievance_status,
                $item->processing_days ?? '—',
                $item->created_at->format('F d, Y — h:i A')
            ]);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return Response::streamDownload(fn() => print($contents), $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportExcel()
    {
        $user = Auth::user();

        $formattedStart = \Carbon\Carbon::parse($this->startDate)->format('F d, Y');
        $formattedEnd = \Carbon\Carbon::parse($this->endDate)->format('F d, Y');

        $type = $this->filterType ?? 'All Types';
        $category = $this->filterCategory ?? 'All Categories';
        $capitalize = fn($str) => ucwords(strtolower($str));
        $type = $capitalize($type);
        $category = $capitalize($category);

        if ($type === 'All Types' && $category === 'All Categories') {
            $dynamicTitle = 'Reports based on All Types in relation to All Categories';
        } elseif ($type !== 'All Types' && $category === 'All Categories') {
            $dynamicTitle = "Reports about $type in relation to All Categories";
        } elseif ($type !== 'All Types' && $category !== 'All Categories') {
            $dynamicTitle = "Reports about $type in relation to $category";
        } else {
            $dynamicTitle = "Reports based on All Types in relation to $category";
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ["MandaueCitiSync Reports"],
            ["Report Generated By:", $user->name],
            ["Department:", $user->departments->pluck('department_name')->join(', ')],
            ["Role:", str_replace('Hr','HR',ucwords(str_replace('_',' ', $user->getRoleNames()->first() ?? 'N/A')))],
            ["Date Range:", "$formattedStart – $formattedEnd"],
            ["Exported At:", now()->format('F d, Y — h:i A')],
            ["Report Title:", $dynamicTitle],
            [],
        ], null, 'A1');

        $headers = ['Ticket ID', 'Title', 'Type', 'Category', 'Status', 'Processing Days', 'Date & Time'];
        $sheet->fromArray($headers, null, 'A9');

        $rowNum = 10;
        foreach ($this->data as $item) {
            $sheet->fromArray([
                $item->grievance_ticket_id,
                $item->grievance_title,
                $item->grievance_type ?? '-',
                $item->grievance_category,
                $item->grievance_status,
                $item->processing_days ?? '—',
                $item->created_at->format('F d, Y — h:i A')
            ], null, 'A' . $rowNum);
            $rowNum++;
        }

        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'mandauecitisync-report-' . now()->format('Y-m-d_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function printReport()
    {
        $this->loadData();

        $cacheKey = 'hr-liaison-report-' . auth()->id() . '-' . now()->timestamp;

        cache()->put($cacheKey, [
            'data' => $this->data,
            'user' => Auth::user(),
            'statuses' => $this->statuses,
            'filterType' => $this->filterType ?? 'All Types',
            'filterCategory' => $this->filterCategory ?? 'All Categories',
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'hrName' => auth()->user()->name,
            'dynamicTitle' => $this->getDynamicTitle(),
        ], now()->addMinutes(10));

        return redirect()->route('print-hr-liaison-reports', ['key' => $cacheKey]);
    }


    public function render()
    {
        return view('livewire.user.hr-liaison.reports-and-analytics.index', [
            'categories' => $this->categories,
        ]);
    }
}
