<?php

namespace App\Livewire\User\Citizen;

use Filament\Notifications\Notification;
use Livewire\Component;
use App\Models\Grievance;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Submission History')]
class SubmissionHistory extends Component
{
    public bool $canRestore = false;
    public int $limit = 5;
    public int $increment = 5;

    public function mount()
    {
        $this->updateRestoreStatus();
    }

    private function updateRestoreStatus(): void
    {
        $this->canRestore = Grievance::where('user_id', auth()->id())
            ->where('is_cleared', true)
            ->whereNull('deleted_at')
            ->exists();
    }

    public function removeFromHistory(int $grievanceId): void
    {
        $grievance = Grievance::where('user_id', auth()->id())
            ->where('grievance_id', $grievanceId)
            ->first();

        if ($grievance) {
            $grievance->update(['is_cleared' => true]);
            $this->updateRestoreStatus();

            Notification::make()
                ->title('Grievance Removed')
                ->success()
                ->body('This grievance has been removed from your history.')
                ->send();
        }
    }

    public function clearHistory(): void
    {
        Grievance::where('user_id', auth()->id())
            ->where('is_cleared', false)
            ->update(['is_cleared' => true]);

        $this->updateRestoreStatus();

        Notification::make()
            ->title('Submission History Cleared')
            ->success()
            ->body('All grievance records have been hidden from your history.')
            ->send();
    }

    public function restoreHistory(): void
    {
        if (! $this->canRestore) {
            Notification::make()
                ->title('Nothing to Restore')
                ->warning()
                ->body('There are no grievances available to restore.')
                ->send();
            return;
        }

        Grievance::where('user_id', auth()->id())
            ->where('is_cleared', true)
            ->whereNull('deleted_at')
            ->update(['is_cleared' => false]);

        $this->updateRestoreStatus();

        Notification::make()
            ->title('History Restored')
            ->success()
            ->body('All grievances have been restored to your history.')
            ->send();
    }

    public function loadMore(): void
    {
        $this->limit += $this->increment;
    }

    public function getGroupedGrievancesProperty()
    {
        $grievances = Grievance::with(['departments', 'attachments'])
            ->where('user_id', auth()->id())
            ->where('is_cleared', false)
            ->whereNull('deleted_at')
            ->latest()
            ->take($this->limit)
            ->get();

        return $grievances->groupBy(function ($grievance) {
            $date = $grievance->created_at->startOfDay();
            $today = now()->startOfDay();
            $yesterday = now()->subDay()->startOfDay();

            return match (true) {
                $date->equalTo($today) => 'Today',
                $date->equalTo($yesterday) => 'Yesterday',
                default => $grievance->created_at->format('F j, Y'),
            };
        });
    }

    public function render()
    {
        $this->updateRestoreStatus();

        $totalCount = Grievance::where('user_id', auth()->id())
            ->where('is_cleared', false)
            ->whereNull('deleted_at')
            ->count();

        return view('livewire.user.citizen.submission-history', [
            'groupedGrievances' => $this->groupedGrievances,
            'canRestore' => $this->canRestore,
            'hasMore' => $totalCount > $this->limit,
        ]);
    }
}
