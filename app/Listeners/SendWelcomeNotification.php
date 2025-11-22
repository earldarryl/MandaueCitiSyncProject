<?php

namespace App\Listeners;

use App\Notifications\GeneralNotification;
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
            'module'       => 'Authentication',
            'action'       => $roleName . ' registered an account',
            'action_type'  => 'register',
            'model_type'   => null,
            'model_id'     => null,
            'description'  => $roleName . ' (' . $user->email . ') successfully registered.',
            'changes'      => [],
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'location'     => geoip(request()->ip())?->city,
            'timestamp'    => now(),
        ]);

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'New User Registration',
                "A new user, {$user->name} ({$user->email}), has registered.",
                'success',
                ['user_id' => $user->id],
                [],
                true,
            ));
        }
    }
}
