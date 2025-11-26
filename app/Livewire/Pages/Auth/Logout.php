<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Auth\Events\Logout as LogoutEvent;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ActivityLog;
class Logout extends Component
{

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            $role = $user->roles->first()?->name ?? 'user';
            $roleName = $role === 'hr_liaison' ? 'HR Liaison' : ucfirst($role);

            ActivityLog::create([
                'user_id'      => $user->id,
                'role_id'      => $user->roles->first()?->id,
                'module'       => 'Authentication',
                'action'       => $roleName . ' logged out',
                'action_type'  => 'logout',
                'model_type'   => null,
                'model_id'     => null,
                'description'  => "{$roleName} ({$user->name}) successfully logged out.",
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
                    'User Logged Out',
                    "{$roleName} ({$user->email}) has logged out.",
                    'info',
                    ['user_id' => $user->id],
                    [],
                    true
                ));

            }

            $user->markOffline();
        }

        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        $this->redirectIntended(default: route('login', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.auth.logout');
    }
}
