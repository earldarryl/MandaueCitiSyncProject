<?php

namespace App\Livewire\Pages\Auth;

use App\Models\ActivityLog;
use Livewire\Component;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Request as RequestFacade;

#[Layout('layouts.guest')]
#[Title('Welcome to Mandaue CitiSync')]
class Login extends Component
{
    public LoginForm $form;
    public string $title = 'Login';
    public string $redirectLink = '';
    public bool $showSuccessModal = false;
    protected $listeners = ['openModalRegister'];

    public function openModalRegister()
    {
        $this->dispatch('open-register-modal');
        $this->reset(["form.email", 'form.password']);
        $this->resetErrorBag(["form.email", 'form.password']);
    }

    public function login()
    {
        $this->validate();

        $result = $this->form->authenticate('citizen');

        $user = auth()->user();
        $roleName = ucfirst($user->roles->first()?->name ?? 'User');

        Session::regenerate();
        Session::forget('password_reset_done');
        Session::put('just_logged_in', true);

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Authentication',
            'action'       => $roleName . ' logged in',
            'action_type'  => 'login',
            'model_type'   => null,
            'model_id'     => null,
            'description'  => $roleName . ' (' . $user->name . ') logged in successfully.',
            'changes'      => [],
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'location'     => geoip(request()->ip())?->city,
            'timestamp'    => now(),
        ]);

        $redirect = $result['redirect'] ?? null;

        if ($redirect) {
            $this->redirectLink = $redirect;
            $this->showSuccessModal = true;
        }
    }


    public function render()
    {
        return view('livewire.pages.auth.login');
    }
}
