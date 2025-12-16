<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Hash;
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
    #[Validate('required|string')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    #[Validate('boolean')]
    public bool $forceLogin = false;
    public int $cooldown = 0;


    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(string $expectedRole): array
    {
        $this->ensureIsNotRateLimited();

        $user = User::where(function ($q) {
            $q->where('email', $this->email)
            ->orWhere('name', $this->email);
        })
        ->first();
        // -------------------- Email does not exist --------------------
        if (! $user) {
            throw ValidationException::withMessages([
                'form.email' => 'The provided email or username does not exist.',
            ]);
        }

        // -------------------- Wrong password --------------------
        if (! Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.password' => 'The password you entered is incorrect.',
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

        if ($activeSession && $this->forceLogin) {
            DB::table('sessions')->where('id', $activeSession->id)->delete();
        }

        // -------------------- Login manually --------------------
        Auth::login($user, $this->remember);
        RateLimiter::clear($this->throttleKey());

        // -------------------- Role validation --------------------
        if (! $user->hasRole($expectedRole)) {
            Auth::logout();

            throw ValidationException::withMessages([
                'status' => 'These credentials do not match our records.',
                'form.email' => 'Invalid email',
                'form.password' => 'Invalid password',
            ]);
        }

        $user->forceFill(['last_seen_at' => now()])->saveQuietly();

        // -------------------- Email verification --------------------
        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return [
                'user' => $user,
                'redirect' => route('verification.notice'),
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
            'user'     => $user,
            'redirect' => $redirect,
            'success'  => 'Login successful! Redirecting...',
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

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $this->cooldown = $seconds;

        throw ValidationException::withMessages([
            'form.email' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
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
