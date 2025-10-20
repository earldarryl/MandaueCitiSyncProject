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
    public function removeFromHistory($grievanceId)
    {
        $grievance = Grievance::where('user_id', auth()->id())
            ->where('grievance_id', $grievanceId)
            ->first();

        if ($grievance) {
            $grievance->update(['is_cleared' => true]);

            Notification::make()
                ->title('Grievance Removed')
                ->success()
                ->body('This grievance has been removed from your history.')
                ->send();
        }
    }

    public function clearHistory()
    {
        Grievance::where('user_id', auth()->id())
            ->where('is_cleared', false)
            ->update(['is_cleared' => true]);

        Notification::make()
            ->title('Submission History Cleared')
            ->success()
            ->body('All grievance records have been hidden from your history.')
            ->send();
    }

    public function restoreHistory()
    {
        Grievance::where('user_id', auth()->id())
            ->where('is_cleared', true)
            ->update(['is_cleared' => false]);

        Notification::make()
            ->title('History Restored')
            ->success()
            ->body('All grievances have been restored to your history.')
            ->send();
    }

    public function render()
    {
        $grievances = Grievance::with(['departments', 'attachments'])
            ->where('user_id', auth()->id())
            ->where('is_cleared', false)
            ->latest()
            ->get();

        return view('livewire.user.citizen.submission-history', [
            'grievances' => $grievances,
        ]);
    }
}
