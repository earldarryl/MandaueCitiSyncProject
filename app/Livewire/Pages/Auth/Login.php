<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use Filament\Notifications\Notification;
use Livewire\Component;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

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

        Session::regenerate();
        Session::forget('password_reset_done');
        Session::put('just_logged_in', true);

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
