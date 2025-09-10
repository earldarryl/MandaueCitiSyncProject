<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Exceptions\RedirectException;

#[Layout('layouts.guest')]
class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';



public function mount(string $token): void
{
    $this->token = $token;
    $this->email = request()->query('email', '');

}






    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            logger('Reset failed: ' . $status);
            return;
        }



        Session::flash('status', __($status));
        logger('Password reset successful');
        $this->redirectRoute('login', navigate: true);
    }

    public function abortConfirmation(){

        logger('Abort Confirmation');
        $this->redirectRoute('login', navigate: true);

    }

public function render()
{

    return view('livewire.pages.auth.reset-password');
}
}
