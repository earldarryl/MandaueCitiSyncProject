<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class TwoFactorAuth extends Component
{
    public string $code = '';
    public $currentOtp;

    public function mount()
    {
        $this->updateOtp();
    }

    #[On('refresh-otp')]
    public function updateOtp()
    {
        $user = Auth::user();
        if (! $user || ! $user->two_factor_secret) {
            $this->currentOtp = null;
            return;
        }

        $google2fa = new Google2FA();
        $this->currentOtp = $google2fa->getCurrentOtp(
            decrypt($user->two_factor_secret)
        );
    }
    public function confirm()
    {
        $user = Auth::user();

        if (! $user || ! $user->two_factor_secret) {
            $this->addError('two_factor', 'Two-Factor is not enabled for this account.');
            return;
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $this->code);

        if (! $valid) {
            $this->addError('two_factor', 'Invalid Two-Factor Authentication code.');
            return;
        }

        session(['two_factor_confirmed' => true]);
        return redirect()->intended('/dashboard');
    }

    public function getCurrentOtpProperty()
    {
        $google2fa = new Google2FA();

        return $google2fa->getCurrentOtp(decrypt(auth()->user()->two_factor_secret));
    }
    public function render()
    {
        return view('livewire.settings.two-factor-auth');
    }
}
