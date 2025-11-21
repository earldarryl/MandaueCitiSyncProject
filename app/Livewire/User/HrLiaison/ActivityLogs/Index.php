<?php

namespace App\Livewire\User\HrLiaison\ActivityLogs;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Department;
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
    public ?string $filter = null;
    public ?string $roleFilter = null;
    public ?string $selectedDate = null;

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
        $filter = $this->filter ?: 'All Modules';
        $role = $this->roleFilter ?: 'All Roles';
        $date = $this->selectedDate ?: 'All Dates';

        return "Activity Logs for {$filter} | {$role} | {$date}";
    }

    protected function getFilteredQuery()
    {
        $user = Auth::user();

        // Get HR Liaison departments
        $departmentIds = DB::table('hr_liaison_departments')
            ->where('hr_liaison_id', $user->id)
            ->pluck('department_id')
            ->toArray();

        // Get HR liaisons in the same departments
        $hrLiaisonIds = DB::table('hr_liaison_departments')
            ->whereIn('department_id', $departmentIds)
            ->pluck('hr_liaison_id')
            ->unique()
            ->toArray();

        // Grievances assigned to these HR liaisons
        $grievanceIds = Assignment::whereIn('hr_liaison_id', $hrLiaisonIds)
            ->pluck('grievance_id')
            ->unique()
            ->toArray();

        // Citizens related to those grievances
        $citizenIds = DB::table('grievances')
            ->whereIn('grievance_id', $grievanceIds)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        // Combine HR liaisons, citizens, and admins
        $userIds = array_unique(array_merge($hrLiaisonIds, $citizenIds));

        return ActivityLog::query()
            ->with('user', 'role')
            ->whereIn('user_id', $userIds)
            ->when($this->filter, fn($q) => $q->where('module', $this->filter))
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
            'filter' => $this->filter,
            'roleFilter' => $this->roleFilter,
            'selectedDate' => $this->selectedDate,
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            [
                'ID', 'Action Type', 'Action', 'Module', 'Platform', 'User', 'Role', 'Timestamp', 'Location',
            ]
        ]);

        $row = 2;

        foreach ($logs as $log) {
            $userName = $log->user?->name ?? 'N/A';
            $roleName = $log->role?->name ?? 'N/A';
            $roleNameFormatted = str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $roleName)));

            $sheet->fromArray([
                [
                    $log->activity_log_id,
                    ucwords(str_replace('_', ' ', $log->action_type)),
                    str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $log->action))),
                    $log->module ?? 'N/A',
                    $log->platform ?? 'N/A',
                    $userName,
                    $roleNameFormatted,
                    $log->timestamp,
                    $log->location ?? 'N/A',
                ]
            ], null, "A{$row}");

            $row++;
        }

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = storage_path($fileName);
        $writer->save($tempFile);

        return Response::download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function downloadCsv()
    {
        $logs = $this->getFilteredQuery()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Action Type', 'Action', 'Module', 'Platform', 'User', 'Role', 'Timestamp', 'Location',
            ]);

            foreach ($logs as $log) {
                $userName = $log->user?->name ?? 'N/A';
                $roleName = $log->role?->name ?? 'N/A';
                $roleNameFormatted = str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $roleName)));

                fputcsv($handle, [
                    $log->activity_log_id,
                    ucwords(str_replace('_', ' ', $log->action_type)),
                    str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $log->action))),
                    $log->module ?? 'N/A',
                    $log->platform ?? 'N/A',
                    $userName,
                    $roleNameFormatted,
                    $log->timestamp,
                    $log->location ?? 'N/A',
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
