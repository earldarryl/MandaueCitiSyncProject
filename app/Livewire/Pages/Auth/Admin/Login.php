<?php

namespace App\Livewire\Pages\Auth\Admin;

use Livewire\Component;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.guest')]
#[Title('Admin | Login')]
class Login extends Component
{
    public LoginForm $form;
    public bool $isOpenModalLogin = false;
    public string $title = 'Admin | Login';
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

        $result = $this->form->authenticate('admin');

        Session::regenerate();
        Session::forget('password_reset_done');
        Session::put('just_logged_in', true);

        $redirect = $result['redirect'] ?? null;

        if ($redirect) {
            $this->redirect($redirect, navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.pages.auth.admin.login');
    }
}
