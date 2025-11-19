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
        $user = $event->user instanceof User
            ? $event->user
            : User::find($event->user->getAuthIdentifier());

        if (! $user) {
            return;
        }

        Notification::make()
            ->title('Welcome, ' . ($user->name ?? $user->email ?? 'User'))
            ->body('Thanks for registering! Explore your dashboard!')
            ->success()
            ->send()
            ->sendToDatabase($user);

        $roleName = ucfirst($user->roles->first()?->name ?? 'user');

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'action'       => $roleName . ' registered an account',
            'action_type'  => 'register',
            'module_name'  => 'Authentication',
            'description'  => $roleName . ' (' . $user->email . ') successfully registered.',
            'ip_address'   => Request::ip(),
            'device_info'  => Request::header('User-Agent'),
            'created_by'   => $user->id,
            'updated_by'   => $user->id,
        ]);

    }
}
