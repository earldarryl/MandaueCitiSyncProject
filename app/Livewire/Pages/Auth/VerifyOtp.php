<?php

namespace App\Livewire\Pages\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.guest')]
#[Title('Verify OTP Page')]
class VerifyOtp extends Component
{
    public string $status = '';
    public string $title = 'Verify Account';
    public int $cooldown = 0;

    private string $limiterKey = '';

    private function getLimiterKey(): string
    {
        if ($this->limiterKey === '') {
            $this->limiterKey = 'otp-send:' . Auth::id();
        }

        return $this->limiterKey;
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(
                default: route('dashboard', absolute: false),
                navigate: true
            );
            return;
        }

        // Check rate limit (1 attempt per 60 seconds)
        if (RateLimiter::tooManyAttempts($this->getLimiterKey(), 1)) {
            $this->cooldown = RateLimiter::availableIn($this->getLimiterKey());
            $this->addError('otp', 'Please wait before resending another OTP.');
            return;
        }

        $this->sendOtpInternal();
    }

    private function sendOtpInternal(): void
    {
        $user = Auth::user();
        $user->sendEmailVerificationNotification();

        RateLimiter::hit($this->getLimiterKey(), 60); // lock for 60s

        session(['status' => 'verification-link-sent']);

        $this->cooldown = 60;
        $this->status = 'verification-link-sent';
    }

    public function render()
    {
        return view('livewire.pages.auth.verify-otp');
    }
}
