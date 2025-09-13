<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

#[Layout('layouts.guest')]
#[Title('Welcome to Mandaue CitiSync')]
class Login extends Component
{
    public LoginForm $form;
    public bool $isOpenModalLogin = false;
    public string $title = 'Login';
    public bool $isButtonShow = false;
    protected $listeners = ['openModalRegister'];

    public function openModalRegister()
    {
        $this->dispatch('open-register-modal');
        $this->reset(["form.email", 'form.password']);
        $this->resetErrorBag(["form.email", 'form.password']);
    }

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        Session::forget('password_reset_done');
        $this->isOpenModalLogin = true;
        $this->isButtonShow = true;

        $user = Auth::user();

        $user->forceFill(['last_seen_at' => now()])->saveQuietly();

        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        } else {
            $roleName = ucfirst(auth()->user()->roles->first()?->name ?? 'user');

            ActivityLog::create([
                'user_id'    => auth()->id(),
                'role_id'    => auth()->user()->roles->first()?->id,
                'action'     => $roleName . ' logged in',
                'ip_address' => Request::ip(),
                'device_info'=> Request::header('User-Agent'),
            ]);

        }

        // ✅ Mark session for dashboard notification
        Session::put('just_logged_in', true);
    }

    public function render()
    {
        return view('livewire.pages.auth.login');
    }
}
