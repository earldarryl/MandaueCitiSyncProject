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
    public string $filter = '';

    public function mount(): void
    {
        $this->updateRestoreStatus();
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

            auth()->user()->notify(new GeneralNotification(
                "Removed from History",
                'This record has been removed from your submission history.',
                'success',
                [],
                [],
                false
            ));
        }
    }

    public function clearHistory(): void
    {
        HistoryLog::where('user_id', auth()->id())->delete();
        $this->updateRestoreStatus();

        auth()->user()->notify(new GeneralNotification(
                "History Cleared",
                'All submission records have been hidden from your history.',
                'success',
                [],
                [],
                false
            ));

    }

    public function restoreHistory(): void
    {
        if (! $this->canRestore) {

            auth()->user()->notify(new GeneralNotification(
                "Nothing to Restore",
                'There are no hidden records available to restore.',
                'warning',
                [],
                [],
                false
            ));
            return;
        }

        HistoryLog::onlyTrashed()
            ->where('user_id', auth()->id())
            ->restore();

        $this->updateRestoreStatus();

        auth()->user()->notify(new GeneralNotification(
            "History Restored",
            'All records have been restored to your submission history.',
            'success',
            [],
            [],
            false
        ));
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

        $query->when($this->filter === 'Grievances', fn($q) => $q->where('reference_table', 'grievances'))
              ->when($this->filter === 'Feedbacks', fn($q) => $q->where('reference_table', 'feedback'));

        $logs = $query->latest()
                      ->take($this->limit)
                      ->get();

        return $logs->groupBy(function ($log) {
            $date = $log->created_at->startOfDay();
            $today = now()->startOfDay();
            $yesterday = now()->subDay()->startOfDay();

            return match (true) {
                $date->equalTo($today) => 'Today',
                $date->equalTo($yesterday) => 'Yesterday',
                default => $log->created_at->format('F j, Y'),
            };
        });
    }

    public function render()
    {
        $this->updateRestoreStatus();

        $totalCount = HistoryLog::where('user_id', auth()->id())
            ->when($this->filter === 'Grievances', fn($q) => $q->where('reference_table', 'grievances'))
            ->when($this->filter === 'Feedbacks', fn($q) => $q->where('reference_table', 'feedback'))
            ->count();

        return view('livewire.user.citizen.submission-history', [
            'groupedLogs' => $this->groupedLogs,
            'canRestore'  => $this->canRestore,
            'hasMore'     => $totalCount > $this->limit,
        ]);
    }
}
