<?php

namespace App\Livewire\User\HrLiaison\ActivityLogs;

use App\Models\ActivityLog;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Response;

#[Layout('layouts.app')]
#[Title('Activity Logs')]
class Index extends Component
{
    use WithPagination;

    public int $limit = 10;
    public ?string $moduleFilter = null;
    public ?string $actionFilter = null;
    public ?string $roleFilter = null;
    public ?string $selectedDate = null;
    public $moduleOptions = [];
    public $actionTypeOptions = [];

    public function mount()
    {
        $this->moduleOptions = ActivityLog::query()
            ->distinct()
            ->pluck('module')
            ->map(fn($type) => ucwords(str_replace('_', ' ', $type)))
            ->toArray();

        $this->actionTypeOptions = ActivityLog::query()
            ->distinct()
            ->pluck('action_type')
            ->map(fn($type) => ucwords(str_replace('_', ' ', $type)))
            ->toArray();
    }

    public function applyFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedDate()
    {
        $this->resetPage();
    }

    public function dynamicLogsTitle(): string
    {
        $moduleFilter = $this->moduleFilter ?: 'All Modules';
        $actionFilter = $this->actionFilter ?: 'All Action';
        $role = $this->roleFilter ?: 'All Roles';
        $date = $this->selectedDate ?: 'All Dates';

        return "Activity Logs for {$moduleFilter} | {$actionFilter} | {$role} | {$date}";
    }

    protected function getFilteredQuery()
    {
        $user = Auth::user();

        $departmentIds = DB::table('hr_liaison_departments')
            ->where('hr_liaison_id', $user->id)
            ->pluck('department_id')
            ->toArray();

        $hrLiaisonIds = DB::table('hr_liaison_departments')
            ->whereIn('department_id', $departmentIds)
            ->pluck('hr_liaison_id')
            ->unique()
            ->toArray();

        $grievanceIds = Assignment::whereIn('hr_liaison_id', $hrLiaisonIds)
            ->pluck('grievance_id')
            ->unique()
            ->toArray();

        $citizenIds = DB::table('grievances')
            ->whereIn('grievance_id', $grievanceIds)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $userIds = array_unique(array_merge($hrLiaisonIds, $citizenIds));

        return ActivityLog::query()
            ->with('user', 'role')
            ->whereIn('user_id', $userIds)
            ->when($this->moduleFilter, fn($q) => $q->where('module', $this->moduleFilter))
            ->when($this->actionFilter, fn($q) => $q->where('action_type', $this->actionFilter))
            ->when($this->roleFilter, function ($q) {
                if ($this->roleFilter === 'Admin') $q->where('role_id', 1);
                elseif ($this->roleFilter === 'HR Liaison') $q->where('role_id', 2);
                elseif ($this->roleFilter === 'Citizen') $q->where('role_id', 3);
            })
            ->when($this->selectedDate, fn($q) => $q->whereDate('timestamp', $this->selectedDate))
            ->latest('timestamp');
    }

    public function exportActivityLogsPDF()
    {
        $logs = $this->getFilteredQuery()->get();

        $html = view('pdf.activity-logs-report', [
            'logs' => $logs,
            'user' => Auth::user(),
            'moduleFilter' => $this->moduleFilter,
            'actionFilter' => $this->actionFilter,
            'roleFilter' => $this->roleFilter,
            'selectedDate' => $this->selectedDate,
            'isAdmin' => false,
            'dynamicTitle' => $this->dynamicLogsTitle(),
        ])->render();

        $pdfPath = storage_path('app/public/activity-logs-report.pdf');

        Browsershot::html($html)
            ->setNodeBinary('C:\Program Files\nodejs\node.exe')
            ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->delay(2000)
            ->timeout(120)
            ->format('A4')
            ->save($pdfPath);

        return response()->download($pdfPath, 'activity-logs-report.pdf');
    }

    public function downloadExcel()
    {
        $logs = $this->getFilteredQuery()->get();
        $currentUserName = auth()->user()->name ?? 'N/A';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', $this->dynamicLogsTitle());
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->fromArray([
            ['ID', 'Action Type', 'Action', 'Module', 'Platform', 'User', 'Role', 'Timestamp', 'Changes']
        ], null, 'A2');

        $row = 3;

        foreach ($logs as $log) {
            $roleName = $log->role?->name ?? 'N/A';
            $roleNameFormatted = str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $roleName)));

            $changes = [];
            if (is_array($log->changes)) {
                foreach ($log->changes as $field => $value) {
                    if (is_array($value)) {
                        $old = isset($value['old']) ? (is_array($value['old']) ? implode(', ', $value['old']) : $value['old']) : null;
                        $new = isset($value['new']) ? (is_array($value['new']) ? implode(', ', $value['new']) : $value['new']) : null;
                        $changes[] = $old !== null ? "$field (OLD: $old, NEW: $new)" : "$field (NEW: $new)";
                    } else {
                        $changes[] = "$field: $value";
                    }
                }
            }

            $sheet->fromArray([
                [
                    $log->activity_log_id,
                    ucwords(str_replace('_', ' ', $log->action_type)),
                    str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $log->action))),
                    $log->module ?? 'N/A',
                    $log->platform ?? 'N/A',
                    $currentUserName,
                    $roleNameFormatted,
                    $log->timestamp,
                    implode('; ', $changes)
                ]
            ], null, "A{$row}");

            $row++;
        }

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = $this->dynamicLogsTitle() . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $fileName = str_replace([' ', '|'], ['_', ''], $fileName);
        $tempFile = storage_path($fileName);
        $writer->save($tempFile);

        return Response::download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function downloadCsv()
    {
        $logs = $this->getFilteredQuery()->get();
        $currentUserName = auth()->user()->name ?? 'N/A';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . str_replace([' ', '|'], ['_', ''], $this->dynamicLogsTitle()) . '_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($logs, $currentUserName) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Action Type', 'Action', 'Module', 'Platform', 'User', 'Role', 'Timestamp', 'Changes']);

            foreach ($logs as $log) {
                $roleName = $log->role?->name ?? 'N/A';
                $roleNameFormatted = str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $roleName)));

                $changes = [];
                if (is_array($log->changes)) {
                    foreach ($log->changes as $field => $value) {
                        if (is_array($value)) {
                            $old = isset($value['old']) ? (is_array($value['old']) ? implode(', ', $value['old']) : $value['old']) : null;
                            $new = isset($value['new']) ? (is_array($value['new']) ? implode(', ', $value['new']) : $value['new']) : null;
                            $changes[] = $old !== null ? "$field (OLD: $old, NEW: $new)" : "$field (NEW: $new)";
                        } else {
                            $changes[] = "$field: $value";
                        }
                    }
                }

                fputcsv($handle, [
                    $log->activity_log_id,
                    ucwords(str_replace('_', ' ', $log->action_type)),
                    str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $log->action))),
                    $log->module ?? 'N/A',
                    $log->platform ?? 'N/A',
                    $currentUserName,
                    $roleNameFormatted,
                    $log->timestamp,
                    implode('; ', $changes),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $logsPaginator = $this->getFilteredQuery()->paginate($this->limit);

        $groupedLogs = collect($logsPaginator->items())->groupBy(function ($log) {
            $date = Carbon::parse($log->timestamp)->startOfDay();
            $today = Carbon::now()->startOfDay();
            $yesterday = Carbon::now()->subDay()->startOfDay();

            if ($date->equalTo($today)) return 'Today';
            if ($date->equalTo($yesterday)) return 'Yesterday';

            return $date->format('F j, Y');
        });

        return view('livewire.user.hr-liaison.activity-logs.index', [
            'logsPaginator' => $logsPaginator,
            'groupedLogs' => $groupedLogs,
        ]);
    }
}
