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
            'action'       => $roleName . ' logged in',
            'action_type'  => 'login',
            'module_name'  => 'Authentication',
            'description'  => $roleName . ' (' . $user->email . ') logged in successfully.',
            'timestamp'    => now(),
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'created_by'   => $user->id,
            'updated_by'   => $user->id,
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
