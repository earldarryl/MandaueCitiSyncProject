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
    public ?string $filter = null;
    public ?string $roleFilter = null;
    public ?string $selectedDate = null;
    public $totalUsers = 0;
    public $activeUsers = 0;
    public $totalOnlineTimeFormatted = '0m';
    public array $modules = [];

    public function applyFilter(): void
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

    public function exportActivityLogsPDF()
    {
        $user = Auth::user();

        $query = ActivityLog::query()
            ->with('user', 'role')
            ->when($this->filter, fn($q) => $q->where('module', $this->filter))
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
            'user' => $user,
            'filter' => $this->filter,
            'roleFilter' => $this->roleFilter,
            'selectedDate' => $this->selectedDate,
            'dynamicTitle' => $this->dynamicLogsTitle(),
            'isAdmin' => $user->hasRole('admin'),
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
            ->when($this->filter, fn($q) => $q->where('module', $this->filter))
            ->when($this->roleFilter, function ($q) {
                if ($this->roleFilter === 'Admin') $q->where('role_id', 1);
                elseif ($this->roleFilter === 'HR Liaison') $q->where('role_id', 2);
                elseif ($this->roleFilter === 'Citizen') $q->where('role_id', 3);
            })
            ->when($this->selectedDate, fn($q) => $q->whereDate('timestamp', $this->selectedDate))
            ->latest('timestamp')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', $this->dynamicLogsTitle());
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:I2');
        $downloadedBy = Auth::user()->name ?? 'Unknown';
        $downloadedRole = ucwords(Auth::user()?->getRoleNames()->first()) ?? 'N/A';
        $sheet->setCellValue('A2', "Downloaded by: {$downloadedBy} ({$downloadedRole}) | Time: " . now()->format('F j, Y – g:i A'));
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->fromArray([
            [
                'ID',
                'Action Type',
                'Action',
                'Module',
                'Platform',
                'User',
                'Role',
                'Timestamp',
                'Location',
            ]
        ], null, 'A3');

        $row = 4;

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
        $logs = ActivityLog::query()
            ->with('user', 'role')
            ->when($this->filter, fn($q) => $q->where('module', $this->filter))
            ->when($this->roleFilter, function ($q) {
                if ($this->roleFilter === 'Admin') $q->where('role_id', 1);
                elseif ($this->roleFilter === 'HR Liaison') $q->where('role_id', 2);
                elseif ($this->roleFilter === 'Citizen') $q->where('role_id', 3);
            })
            ->when($this->selectedDate, fn($q) => $q->whereDate('timestamp', $this->selectedDate))
            ->latest('timestamp')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [$this->dynamicLogsTitle()]);
            $downloadedBy = Auth::user()->name ?? 'Unknown';
            $downloadedRole = ucwords(Auth::user()?->getRoleNames()->first()) ?? 'N/A';
            fputcsv($handle, ["Downloaded by: {$downloadedBy} ({$downloadedRole}) | Time: " . now()->format('F j, Y – g:i A')]);

            fputcsv($handle, []);

            fputcsv($handle, [
                'ID',
                'Action Type',
                'Action',
                'Module',
                'Platform',
                'User',
                'Role',
                'Timestamp',
                'Location',
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
        $this->totalUsers = User::count();
        $this->activeUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->count();

        $onlineUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->get();

        $totalMinutes = 0;

        foreach ($onlineUsers as $user) {
            $minutes = Carbon::parse($user->last_seen_at)->diffInMinutes(now());
            $totalMinutes += $minutes;
        }

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0) {
            $this->totalOnlineTimeFormatted = "{$hours}h {$minutes}m";
        } else {
            $this->totalOnlineTimeFormatted = "{$minutes}m";
        }


        $this->modules = ActivityLog::query()
            ->whereNotNull('module')
            ->select('module')
            ->distinct()
            ->pluck('module')
            ->toArray();

        $query = ActivityLog::query()
            ->with('user', 'role')
            ->when($this->filter, fn($q) => $q->where('module', $this->filter))
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
