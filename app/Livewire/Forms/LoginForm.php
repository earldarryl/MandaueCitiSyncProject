<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
                'form.email' => 'The provided email address does not exist.',
            ]);
        }

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.password' => 'The provided password is incorrect.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        $user = Auth::user();

        if (! $user->hasRole($expectedRole)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'form.email' => 'These credentials do not match our records.',
            ]);
        }

        $user->forceFill(['last_seen_at' => now()])->saveQuietly();

        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return [
                'user' => $user,
                'redirect' => null,
            ];
        }

        $role = strtolower($user->roles->first()?->name ?? 'user');

        $redirect = match ($role) {
            'admin'      => route('dashboard'),
            'hr_liaison' => route('dashboard'),
            'citizen'    => route('grievance.create'),
            default      => route('dashboard'),
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
