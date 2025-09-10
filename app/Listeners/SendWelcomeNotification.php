<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class SendWelcomeNotification
{
    public function handle(Registered $event): void
    {
        // Make sure we are using App\Models\User
        $user = $event->user instanceof User
            ? $event->user
            : User::find($event->user->getAuthIdentifier());

        if (! $user) {
            return;
        }

        // Send the welcome notification
        Notification::make()
            ->title('Welcome, ' . ($user->name ?? $user->email ?? 'User') . ' 🎉')
            ->body('Thanks for registering! Explore your dashboard!')
            ->success()
            ->send()
            ->sendToDatabase($user);

        // Record an activity log for registration
        $roleName = ucfirst($user->roles->first()?->name ?? 'user');

        ActivityLog::create([
            'user_id'    => $user->id,
            'role_id'    => $user->roles->first()?->id,
            'action'     => $roleName . ' registered an account',
            'ip_address' => Request::ip(),
            'device_info'=> Request::header('User-Agent'),
        ]);
    }
}
