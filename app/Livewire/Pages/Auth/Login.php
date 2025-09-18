<?php

namespace App\Livewire\Pages\Auth;

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
    public bool $isOpenModalLogin = false;
    public string $title = 'Login';
    public string $redirectLink = '';
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

        $result = $this->form->authenticate('citizen');

        Session::regenerate();
        Session::forget('password_reset_done');
        $this->isOpenModalLogin = true;
        $this->isButtonShow = true;

        $user = $result['user'];
        $redirect = $result['redirect'];

        if ($redirect) {
            $this->redirectLink = $redirect;
        }

        Session::put('just_logged_in', true);
    }

    public function render()
    {
        return view('livewire.pages.auth.login');
    }
}
