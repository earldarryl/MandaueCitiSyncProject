<?php

namespace App\Livewire\User\Citizen;

use App\Models\HistoryLog;
use App\Notifications\GeneralNotification;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('Submission History')]
class SubmissionHistory extends Component
{
    public bool $canRestore = false;
    public int $limit = 5;
    public int $increment = 5;
    public ?string $selectedDate = null;
    public string $filter = '';
    public array $actionTypeOptions = [];

    public function mount(): void
    {
        $this->updateRestoreStatus();

        $this->actionTypeOptions = HistoryLog::withTrashed()
            ->where('user_id', auth()->id())
            ->select('action_type')
            ->distinct()
            ->pluck('action_type')
            ->map(fn($type) => ucwords(str_replace('_', ' ', $type)))
            ->toArray();

    }

    private function updateRestoreStatus(): void
    {
        $this->canRestore = HistoryLog::onlyTrashed()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function removeFromHistory(int $logId): void
    {
        $log = HistoryLog::where('user_id', auth()->id())
            ->where('id', $logId)
            ->first();

        if ($log) {
            $log->delete();
            $this->updateRestoreStatus();

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Removed from History',
                'message' => 'This record has been removed from your submission history.',
            ]);
        }
    }

    public function clearHistory(): void
    {
        $logs = HistoryLog::where('user_id', auth()->id())
            ->whereNull('deleted_at')
            ->get();

        $hasLogs = $logs->filter(function ($log) {
            if ($log->reference_table === 'grievances') {
                return \App\Models\Grievance::where('grievance_id', $log->reference_id)->exists();
            } elseif ($log->reference_table === 'feedback') {
                return \App\Models\Feedback::where('id', $log->reference_id)->exists();
            }
            return true;
        });

        if ($hasLogs->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'Nothing to Clear',
                'message' => 'All submission records have already been deleted or hidden.',
            ]);
            return;
        }

        foreach ($logs as $log) {
            $deleteHard = false;

            if ($log->reference_table === 'grievances') {
                $deleteHard = !\App\Models\Grievance::where('grievance_id', $log->reference_id)->exists();
            } elseif ($log->reference_table === 'feedback') {
                $deleteHard = !\App\Models\Feedback::where('id', $log->reference_id)->exists();
            }

            if ($deleteHard) {
                $log->forceDelete();
            } else {
                $log->delete();
            }
        }

        $this->updateRestoreStatus();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'History Cleared',
            'message' => 'All submission records have been cleared from your history.',
        ]);
    }

    public function restoreHistory(): void
    {
        if (! $this->canRestore) {

            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'Nothing to Restore',
                'message' => 'There are no cleared records available to restore.',
            ]);
            return;
        }

        HistoryLog::onlyTrashed()
            ->where('user_id', auth()->id())
            ->restore();

        $this->updateRestoreStatus();

        $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'History Restored',
                'message' => 'All records have been restored to your submission history.',
            ]);

    }

    public function loadMore(): void
    {
        $this->limit += $this->increment;
    }

    public function applyFilter(): void
    {
        $this->limit = 5;
    }

    public function getGroupedLogsProperty()
    {
        $query = HistoryLog::where('user_id', auth()->id());

        if ($this->filter === 'Reports') {
            $query->where('reference_table', 'grievances')
                ->whereExists(fn($subQuery) =>
                    $subQuery->selectRaw(1)
                            ->from('grievances')
                            ->whereColumn('grievances.grievance_id', 'history_logs.reference_id')
                );
        } elseif ($this->filter === 'Feedbacks') {
            $query->where('reference_table', 'feedback')
                ->whereExists(fn($subQuery) =>
                    $subQuery->selectRaw(1)
                            ->from('feedback')
                            ->whereColumn('feedback.id', 'history_logs.reference_id')
                );
        } elseif ($this->filter) {
            $query->where('action_type', strtolower(preg_replace('/[\s-]+/', '_', $this->filter)));
            $query->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('reference_table', 'grievances')
                    ->whereExists(fn($subQuery) =>
                            $subQuery->selectRaw(1)
                                    ->from('grievances')
                                    ->whereColumn('grievances.grievance_id', 'history_logs.reference_id')
                        );
                })
                ->orWhere(function ($q2) {
                    $q2->where('reference_table', 'feedback')
                    ->whereExists(fn($subQuery) =>
                            $subQuery->selectRaw(1)
                                    ->from('feedback')
                                    ->whereColumn('feedback.id', 'history_logs.reference_id')
                        );
                });
            });
        }

        if ($this->selectedDate) {
            $query->whereDate('created_at', $this->selectedDate);
        }

        $logs = $query->latest()->get();

        $grouped = $logs->groupBy(function ($log) {
            $date = $log->created_at->startOfDay();
            $today = now()->startOfDay();
            $yesterday = now()->subDay()->startOfDay();

            return match (true) {
                $date->equalTo($today) => 'Today',
                $date->equalTo($yesterday) => 'Yesterday',
                default => $log->created_at->format('F j, Y'),
            };
        });

        return $grouped->map(fn($items) => $items->take($this->limit))
                    ->filter(fn($items) => $items->count() > 0);
    }

    public function render()
    {
        $this->updateRestoreStatus();

        $groupedLogs = $this->groupedLogs;

        $totalVisibleCount = $groupedLogs->sum(fn($items) => $items->count());

        return view('livewire.user.citizen.submission-history', [
            'groupedLogs' => $this->groupedLogs,
            'canRestore'  => $this->canRestore,
            'hasMore'     => $totalVisibleCount > $this->limit,
            'hasAny'      => $totalVisibleCount > 0,
        ]);
    }

}
