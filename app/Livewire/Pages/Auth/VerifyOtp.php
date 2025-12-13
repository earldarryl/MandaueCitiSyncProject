<?php

namespace App\Livewire\Pages\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request as RequestFacade;

#[Layout('layouts.guest')]
#[Title('Verify OTP Page')]
class VerifyOtp extends Component
{
    public string $status = '';
    public string $title = 'Verify Account';
    public int $cooldown = 0;
    public string $otp = '';
    private string $limiterKey = '';
    public string $textSuccessMessage;
    public function mount()
    {
        $this->cooldown = RateLimiter::availableIn($this->getLimiterKey());

        if ($this->cooldown > 0) {
            $this->startPolling();
        }

        $trigger = request()->query('trigger');

        if ($trigger && session('email_verify_trigger') === $trigger) {
            session()->forget('email_verify_trigger');

            $this->sendOtpInternal();
        }
    }

    public function updateCooldown()
    {
        $this->cooldown = RateLimiter::availableIn($this->getLimiterKey());

        if ($this->cooldown <= 0) {
            $this->stopPolling();
        }
    }

    private function startPolling()
    {
        //
    }

    private function stopPolling()
    {
        //
    }

    private function getLimiterKey(): string
    {
        if ($this->limiterKey === '') {
            $this->limiterKey = 'otp-send:' . Auth::id();
        }

        return $this->limiterKey;
    }

    public function sendVerification()
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(
                default: route('citizen.grievance.index', absolute: false),
                navigate: true
            );
            return;
        }

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

        RateLimiter::hit($this->getLimiterKey(), 60);

        session(['status' => 'verification-link-sent']);

        $this->cooldown = 60;
        $this->status = 'verification-link-sent';

        $this->startPolling();
    }

    public function verifyOtp(): void
    {
        $this->validate([
            'otp' => 'required|numeric',
            ],
            [
                'otp.required' => 'Please enter the OTP sent to your email.',
                'otp.numeric'  => 'OTP must be numbers only.',
            ]
        );

        $user = Auth::user();
        $cachedOtp = Cache::get('email_otp_' . $user->email);

        if ($cachedOtp && $cachedOtp == $this->otp) {
            $user->markEmailAsVerified();

            $roleName = ucfirst($user->roles->first()?->name ?? 'user');

            ActivityLog::create([
                'user_id'      => $user->id,
                'role_id'      => $user->roles->first()?->id,
                'action'       => $roleName . ' logged in',
                'action_type'  => 'login',
                'module_name'  => 'Authentication',
                'description'  => $roleName . ' (' . $user->email . ') logged in successfully.',
                'timestamp'    => now(),
                'ip_address'   => RequestFacade::ip(),
                'device_info'  => RequestFacade::header('User-Agent'),
                'created_by'   => $user->id,
                'updated_by'   => $user->id,
            ]);

            $this->textSuccessMessage = 'Email verified successfully.';

            $this->redirectIntended(
                default: route('citizen.grievance.index', absolute: false),
                navigate: true
            );

            return;
        }

        $this->addError('otp', 'Invalid or expired OTP code.');
    }

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            $user->markOffline();
        }

        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        $this->redirectIntended(default: route('login', absolute: false), navigate: true);
    }
    public function render()
    {
        return view('livewire.pages.auth.verify-otp');
    }
}
