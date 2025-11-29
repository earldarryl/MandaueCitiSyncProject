<?php

namespace App\Livewire\User\Admin\AdminActivityLogs;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Spatie\Browsershot\Browsershot;
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
    public $totalUsers = 0;
    public $activeUsers = 0;
    public $totalOnlineTimeFormatted = '0m';
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

    private function updateOnlineStats()
    {
        $query = ActivityLog::query()
            ->with('user', 'role')
            ->when($this->moduleFilter, fn($q) => $q->where('module', $this->moduleFilter))
            ->when($this->actionFilter, fn($q) => $q->where('action_type', $this->actionFilter))
            ->when($this->roleFilter, function ($q) {
                if ($this->roleFilter === 'Admin') $q->where('role_id', 1);
                elseif ($this->roleFilter === 'HR Liaison') $q->where('role_id', 2);
                elseif ($this->roleFilter === 'Citizen') $q->where('role_id', 3);
            })
            ->when($this->selectedDate, fn($q) => $q->whereDate('timestamp', $this->selectedDate));

        $userIds = $query->pluck('user_id')->unique();

        $this->totalUsers = $userIds->count();

        $onlineUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->get();

        $this->activeUsers = $onlineUsers->count();

        $totalSeconds = 0;

        foreach ($onlineUsers as $user) {
            if ($user->first_online_at) {
                $totalSeconds += $user->first_online_at->diffInSeconds(now());
            }
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);

        $this->totalOnlineTimeFormatted = $hours > 0
            ? "{$hours}h {$minutes}m"
            : "{$minutes}m";

    }

    public function applyFilter(): void
    {
        $this->resetPage();
        $this->updateOnlineStats();
    }


    public function dynamicLogsTitle(): string
    {
        $moduleFilter = $this->moduleFilter ?: 'All Modules';
        $actionFilter = $this->actionFilter ?: 'All Action';
        $role = $this->roleFilter ?: 'All Roles';
        $date = $this->selectedDate ?: 'All Dates';

        return "Activity Logs for {$moduleFilter} | {$actionFilter} | {$role} | {$date}";
    }

    public function exportActivityLogsPDF()
    {
        $user = Auth::user();

        $query = ActivityLog::query()
            ->with('user', 'role')
            ->when($this->moduleFilter, fn($q) => $q->where('module', $this->moduleFilter))
            ->when($this->actionFilter, fn($q) => $q->where('action_type', $this->actionFilter))
            ->when($this->roleFilter, function ($q) {
                if ($this->roleFilter === 'Admin') $q->where('role_id', 1);
                elseif ($this->roleFilter === 'HR Liaison') $q->where('role_id', 2);
                elseif ($this->roleFilter === 'Citizen') $q->where('role_id', 3);
            })
            ->when($this->selectedDate, fn($q) => $q->whereDate('timestamp', $this->selectedDate))
            ->latest('timestamp');

        if ($user->hasRole('hr_liaison')) {
            $query->where('user_id', $user->id);
        }

        $logs = $query->get();


        $html = view('pdf.activity-logs-report', [
            'logs' => $logs,
            'user' => Auth::user(),
            'moduleFilter' => $this->moduleFilter,
            'actionFilter' => $this->actionFilter,
            'roleFilter' => $this->roleFilter,
            'selectedDate' => $this->selectedDate,
            'isAdmin' => true,
            'dynamicTitle' => $this->dynamicLogsTitle(),
            'totalUsers' => $this->totalUsers,
            'activeUsers' => $this->activeUsers,
            'totalOnlineTimeFormatted' => $this->totalOnlineTimeFormatted,
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
        $logs = ActivityLog::query()
            ->with('user', 'role')
            ->when($this->moduleFilter, fn($q) => $q->where('module', $this->moduleFilter))
            ->when($this->actionFilter, fn($q) => $q->where('action_type', $this->actionFilter))
            ->when($this->roleFilter, function ($q) {
                if ($this->roleFilter === 'Admin') $q->where('role_id', 1);
                elseif ($this->roleFilter === 'HR Liaison') $q->where('role_id', 2);
                elseif ($this->roleFilter === 'Citizen') $q->where('role_id', 3);
            })
            ->when($this->selectedDate, fn($q) => $q->whereDate('timestamp', $this->selectedDate))
            ->latest('timestamp')
            ->get();
        $currentUserName = auth()->user()->name ?? 'N/A';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', $this->dynamicLogsTitle());
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', "Total Users: {$this->totalUsers}");
        $sheet->setCellValue('B2', "Active Users: {$this->activeUsers}");
        $sheet->setCellValue('C2', "Total Online Time: {$this->totalOnlineTimeFormatted}");

        $sheet->fromArray([
            ['ID', 'Action Type', 'Action', 'Module', 'Platform', 'User', 'Role', 'Timestamp', 'Changes']
        ], null, 'A4');

        $row = 5;

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
        $logs = ActivityLog::query()
            ->with('user', 'role')
            ->when($this->moduleFilter, fn($q) => $q->where('module', $this->moduleFilter))
            ->when($this->actionFilter, fn($q) => $q->where('action_type', $this->actionFilter))
            ->when($this->roleFilter, function ($q) {
                if ($this->roleFilter === 'Admin') $q->where('role_id', 1);
                elseif ($this->roleFilter === 'HR Liaison') $q->where('role_id', 2);
                elseif ($this->roleFilter === 'Citizen') $q->where('role_id', 3);
            })
            ->when($this->selectedDate, fn($q) => $q->whereDate('timestamp', $this->selectedDate))
            ->latest('timestamp')
            ->get();
        $currentUserName = auth()->user()->name ?? 'N/A';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . str_replace([' ', '|'], ['_', ''], $this->dynamicLogsTitle()) . '_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($logs, $currentUserName) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ["Total Users", $this->totalUsers]);
            fputcsv($handle, ["Active Users", $this->activeUsers]);
            fputcsv($handle, ["Total Online Time", $this->totalOnlineTimeFormatted]);
            fputcsv($handle, []);

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
        $this->totalUsers = User::count();
        $this->activeUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->count();

        $onlineUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->get();

        $this->activeUsers = $onlineUsers->count();

        $totalSeconds = 0;

        foreach ($onlineUsers as $user) {
            if ($user->first_online_at) {
                $totalSeconds += $user->first_online_at->diffInSeconds(now());
            }
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);

        $this->totalOnlineTimeFormatted = $hours > 0
            ? "{$hours}h {$minutes}m"
            : "{$minutes}m";

        $query = ActivityLog::query()
            ->with('user', 'role')
            ->when($this->moduleFilter, fn($q) => $q->where('module', $this->moduleFilter))
            ->when($this->actionFilter, fn($q) => $q->where('action_type', $this->actionFilter))
            ->when($this->roleFilter, function ($q) {
                if ($this->roleFilter === 'Admin') $q->where('role_id', 1);
                elseif ($this->roleFilter === 'HR Liaison') $q->where('role_id', 2);
                elseif ($this->roleFilter === 'Citizen') $q->where('role_id', 3);
            })
            ->when($this->selectedDate, fn($q) => $q->whereDate('timestamp', $this->selectedDate))
            ->latest('timestamp');

        $logsPaginator = $query->paginate($this->limit);

        $groupedLogs = collect($logsPaginator->items())->groupBy(function ($log) {
            $date = Carbon::parse($log->timestamp)->startOfDay();
            $today = Carbon::now()->startOfDay();
            $yesterday = Carbon::now()->subDay()->startOfDay();

            if ($date->equalTo($today)) return 'Today';
            if ($date->equalTo($yesterday)) return 'Yesterday';

            return $date->format('F j, Y');
        });

        return view('livewire.user.admin.admin-activity-logs.index', [
            'logsPaginator' => $logsPaginator,
            'groupedLogs' => $groupedLogs,
        ]);
    }
}
