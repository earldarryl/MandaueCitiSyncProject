<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Auth\Events\Registered;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Register extends Component
{
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $suffix = '';
    public string $gender = '';
    public string $civil_status = '';
    public string $barangay = '';
    public string $sitio = '';
    public string $birthdate = '';
    public int $age;
    public string $name = '';
    public string $email = '';
    public string $contact = '';
    public string $password = '';
    public $profile_pic;
    public $showConfirmModal;
    public string $password_confirmation = '';
    public string $current_password = '';
    public bool $agreed_terms = false;

    protected $listeners = [
        'reset-register-form' => 'resetForm',
        'register-form' => 'register',
    ];

    public function resetForm()
    {
        $this->reset([
            'first_name',
            'middle_name',
            'last_name',
            'suffix',
            'gender',
            'civil_status',
            'barangay',
            'sitio',
            'birthdate',
            'name',
            'email',
            'contact',
            'password',
            'password_confirmation',
            'agreed_terms',
            'profile_pic',
        ]);

        $this->resetErrorBag();
    }

    protected function userRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'contact' => ['required', 'regex:/^9\d{9}$/'],
            'password' => ['required', 'string', 'min:8', \Illuminate\Validation\Rules\Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    protected function userInfoRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\-]+$/'],
            'middle_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\-]+$/'],
            'suffix' => ['required', 'string', 'max:5', 'regex:/^[A-Za-z\s\/\.\-]+$/'],
            'gender' => ['required', 'string', 'regex:/^[A-Za-z\s\-]+$/'],
            'civil_status' => ['required', 'string', 'regex:/^[A-Za-z\s\-]+$/'],
            'barangay' => ['required', 'string', 'regex:/^[A-Za-z\s\-]+$/'],
            'sitio' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
        ];
    }

    public function validateStepOne()
    {
        try {
            $this->validate($this->userInfoRules());
            $this->dispatch('step-one-validated', success: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->showConfirmModal = true;
            $this->dispatch('step-one-validated', success: false);
            throw $e;
        }
    }

    public function validateStepTwo()
    {
        try {
            $this->validate($this->userRules());
            $this->dispatch('step-two-validated', success: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->showConfirmModal = true;
            $this->dispatch('step-two-validated', success: false);
            throw $e;
        }
    }
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\-]+$/'],
            'middle_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\-]+$/'],
            'suffix' => ['required', 'string', 'max:5', 'regex:/^[A-Za-z\s\/\.\-]+$/'],
            'gender' => ['required', 'string', 'regex:/^[A-Za-z\s\-]+$/'],
            'civil_status' => ['required', 'string', 'regex:/^[A-Za-z\s\-]+$/'],
            'barangay' => ['required', 'string', 'regex:/^[A-Za-z\s\-]+$/'],
            'sitio' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/', 'unique:users,email'],
            'contact' => ['required', 'regex:/^9\d{9}$/'], // Starts with 9 + 9 digits (e.g., 9123456789)
            'password' => ['required', 'string', 'min:8', \Illuminate\Validation\Rules\Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
            'agreed_terms' => ['accepted'],
        ];
    }

    public function updatedBirthdate($value)
    {
        $birthdate = \Carbon\Carbon::parse($value);
        $this->age = $birthdate->age;
    }

    protected function messages()
    {
        return [
            'birthdate.before_or_equal' => 'You must be at least 18 years old to register.',
        ];
    }

    public function register()
    {
        try {

            if (!$this->agreed_terms) {
                $this->addError('agreed_terms', 'You must agree to the terms and conditions before proceeding.');

                $this->dispatch('check-terms-error');

                return;
            }

            $validated = $this->validate();

            $validated['contact'] = '+63' . $validated['contact'];

            $user = User::create([
                'name' => ucwords(strtolower(trim($validated['name']))),
                'email' => strtolower(trim($validated['email'])),
                'profile_pic' => null,
                'password' => Hash::make($validated['password']),
                'contact' => trim($validated['contact']),
                'agreed_terms' => true,
                'agreed_at' => now(),
            ]);

            $user->assignRole('citizen');

            $user->info()->create([
                'first_name' => ucwords(strtolower(trim($validated['first_name']))),
                'middle_name' => ucwords(strtolower(trim($validated['middle_name']))),
                'last_name' => ucwords(strtolower(trim($validated['last_name']))),
                'suffix' => trim($validated['suffix']) === 'N/A' ? null : ucwords(strtolower(trim($validated['suffix']))),
                'gender' => ucwords(strtolower(trim($validated['gender']))),
                'civil_status' => ucwords(strtolower(trim($validated['civil_status']))),
                'barangay' => ucwords(strtolower(trim($validated['barangay']))),
                'sitio' => ucwords(strtolower(trim($validated['sitio']))),
                'birthdate' => $validated['birthdate'],
                'age' => $this->age,
            ]);

            event(new Registered($user));
            Auth::login($user);

            $this->dispatch('registration-success');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Registration failed: ' . $e->getMessage());
            $this->addError('register', 'Something went wrong. Please try again.');
        }

        $this->dispatch('register-finished');
        session()->put('just_registered', true);

        return $this->redirectIntended(route('citizen.grievance.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.auth.register');
    }
}
