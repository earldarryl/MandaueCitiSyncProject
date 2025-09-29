<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    #[Validate('boolean')]
    public bool $forceLogin = false; // <-- optional "Force login" toggle

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(string $expectedRole): array
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'status' => 'The provided email address does not exist.',
            ]);
        }

        // -------------------- Check for other active sessions --------------------
        $currentSessionId = Session::getId();
        $activeSession = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '<>', $currentSessionId)
            ->first();

        if ($activeSession && ! $this->forceLogin) {
            throw ValidationException::withMessages([
                'status' => 'Your account is currently logged in from another device/browser.',
            ]);
        }

        // If forceLogin is true, delete the old session
        if ($activeSession && $this->forceLogin) {
            DB::table('sessions')->where('id', $activeSession->id)->delete();
        }

        // -------------------- Attempt login --------------------
        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'status' => 'These credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        $user = Auth::user();

        if (! $user->hasRole($expectedRole)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'status' => 'These credentials do not match our records.',
            ]);
        }

        $user->forceFill(['last_seen_at' => now()])->saveQuietly();

        // -------------------- Email verification --------------------
        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return [
                'user' => $user,
                'redirect' => null,
            ];
        }

        // -------------------- Role-based redirect --------------------
        $role = strtolower($user->roles->first()?->name ?? 'user');

        $redirect = match ($role) {
            'admin'      => route('admin.dashboard'),
            'hr_liaison' => route('hr-liaison.dashboard'),
            'citizen'    => route('citizen.grievance.index'),
            default      => route('login'),
        };

        return [
            'user' => $user,
            'redirect' => $redirect,
        ];
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
