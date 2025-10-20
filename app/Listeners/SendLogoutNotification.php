<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use Filament\Notifications\Notification;
use App\Models\User;

class SendLogoutNotification
{
    public function handle($event): void
    {
        $authUser = $event->user;
        if (! $authUser instanceof User) return;

        $liaisonIds = \App\Models\Assignment::whereHas('grievance', function ($q) use ($authUser) {
                $q->where('user_id', $authUser->id);
            })
            ->pluck('hr_liaison_id')
            ->unique()
            ->filter();

        $liaisons = User::whereIn('id', $liaisonIds)->get();

        foreach ($liaisons as $liaison) {
            $notification = Notification::make()
                ->title('Citizen Logged Out')
                ->body("{$authUser->name} has logged out of the system.")
                ->success()
                ->sendToDatabase($liaison);
        }
    }
}

