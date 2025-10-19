<?php

namespace App\Livewire\Pages\Auth;

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
            $roleName = ucfirst($user->roles->first()?->name ?? 'user');

            ActivityLog::create([
                'user_id'    => $user->id,
                'role_id'    => $user->roles->first()?->id,
                'action'     => $roleName . ' logged out',
                'ip_address' => request()->ip(),
                'device_info'=> request()->header('User-Agent'),
            ]);
        }

        if ($user) {
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
