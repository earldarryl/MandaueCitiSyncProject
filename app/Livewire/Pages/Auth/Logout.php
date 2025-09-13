<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ActivityLog;
use Livewire\Attributes\On;
class Logout extends Component
{

    public $show = false;

    #[On('logout-modal')]
    public function openLogOutModal()
    {
        $this->dispatch('logout-modal-started');

        $this->show = true;

        $this->dispatch('logout-modal-finished');
    }

    public function close()
    {
        $this->show = false;
    }
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
