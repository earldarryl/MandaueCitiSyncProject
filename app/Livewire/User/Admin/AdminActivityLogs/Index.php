<?php

namespace App\Livewire\User\Admin\AdminActivityLogs;

use Livewire\Component;
use App\Models\ActivityLog;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Activity Logs')]
class Index extends Component
{
    use WithPagination;

    public int $limit = 10;
    public ?string $filter = null;
    public ?string $roleFilter = null;

    protected $queryString = ['filter', 'roleFilter'];

    public function applyFilter(): void
    {
        $this->resetPage();
    }

    public function loadMore(): void
    {
        $this->limit += 10;
    }

    public function render()
    {
        $query = ActivityLog::query()
            ->with('user', 'role')
            ->when($this->filter, fn($q) => $q->where('module', $this->filter))
            ->when($this->roleFilter, function ($q) {
                if ($this->roleFilter === 'Admin') {
                    $q->where('role_id', 1);
                } elseif ($this->roleFilter === 'HR Liaison') {
                    $q->where('role_id', 2);
                } elseif ($this->roleFilter === 'Citizen') {
                    $q->where('role_id', 3);
                }
            })
            ->latest('timestamp');

        $logs = $query->paginate($this->limit);

        $groupedLogs = collect($logs->items())->groupBy(function ($log) {
            $date = Carbon::parse($log->timestamp)->startOfDay();
            $today = Carbon::now()->startOfDay();
            $yesterday = Carbon::now()->subDay()->startOfDay();

            if ($date->equalTo($today)) return 'Today';
            if ($date->equalTo($yesterday)) return 'Yesterday';

            return $date->format('F j, Y');
        });

        return view('livewire.user.admin.admin-activity-logs.index', [
            'logs' => $logs,
            'groupedLogs' => $groupedLogs,
            'hasMore' => $logs->hasMorePages(),
        ]);
    }
}
