<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.guest')]
#[Title('Forgot Password')]
class ForgotPassword extends Component
{
    public string $email = '';
    public string $title = 'Forgot Password';
    public function sendPasswordResetLink(): void
    {

        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink($this->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {

            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));


    }
    public function render()
    {
        return view('livewire.pages.auth.forgot-password');
    }
}
