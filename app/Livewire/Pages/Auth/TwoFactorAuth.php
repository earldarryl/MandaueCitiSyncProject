<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
#[Title('Two Factor Authentication')]
class TwoFactorAuth extends Component
{
    public string $code = '';
    public $currentOtp;

    public function mount()
    {
        $this->updateOtp();
    }

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

        if (empty($this->code)) {
            $this->addError('two_factor', 'Please enter your authentication code.');
            return;
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $this->code);

        if (! $valid) {
            $this->addError('two_factor', 'Invalid Two-Factor Authentication code.');
            return;
        }

        session(['two_factor_confirmed' => true]);

        if ($user->hasRole('citizen')) {
            return redirect()->route('citizen.grievance.index');
        }

        if ($user->hasRole('hr_liaison')) {
            return redirect()->route('hr-liaison.dashboard');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect('/dashboard');
    }

    public function getCurrentOtpProperty()
    {
        $google2fa = new Google2FA();

        return $google2fa->getCurrentOtp(decrypt(auth()->user()->two_factor_secret));
    }

    public function render()
    {
        return view('livewire.pages.auth.two-factor-auth');
    }
}
