<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Auth\Events\Registered;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

#[Layout('layouts.guest')]
class Register extends Component
{
    use WithFileUploads;
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $suffix = '';
    public string $gender = '';
    public string $civil_status = '';
    public string $barangay = '';
    public string $sitio = '';
    public string $birthdate ='';
    public int $age;
    public ?TemporaryUploadedFile $profile_pic = null;
    public string $name = '';
    public string $email = '';
    public string $contact = '';
    public string $password = '';
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
        ]);

       $this->resetErrorBag([
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
        ]);

    }


    public function mount()
    {
        if ($this->profile_pic instanceof TemporaryUploadedFile) {
        $filename = $this->profile_pic->store('images', 'public'); // returns 'images/filename.jpg'
        $this->profile_pic = basename($filename); // store just the filename for later
        }

    }


// ...

// User account fields
protected function userProperties(): array
{
    return [
        'name' => $this->name,
        'email' => $this->email,
        'contact' => $this->contact,
        'password' => $this->password,
        'password_confirmation' => $this->password_confirmation,
    ];
}

protected function userRules(): array
{
    return [
        'name' => ['required','string','max:255'],
        'email' => ['required','email','unique:users,email'],
        'contact' => ['required','regex:/^9\d{9}$/'],
        'password' => ['required','string','min:8', \Illuminate\Validation\Rules\Password::defaults()],
        'password_confirmation' => ['required','same:password'],
    ];
}

// User info fields
protected function userInfoProperties(): array
{
    return [
        'first_name' => $this->first_name,
        'middle_name' => $this->middle_name,
        'last_name' => $this->last_name,
        'suffix' => $this->suffix,
        'gender' => $this->gender,
        'civil_status' => $this->civil_status,
        'barangay' => $this->barangay,
        'sitio' => $this->sitio,
        'birthdate' => $this->birthdate,
    ];
}

protected function userInfoRules(): array
{
    return [
        'first_name' => ['required','string','max:255','regex:/^[A-Za-z\s\-]+$/'],
        'middle_name' => ['required','string','max:255','regex:/^[A-Za-z\s\-]+$/'],
        'last_name' => ['required','string','max:255','regex:/^[A-Za-z\s\-]+$/'],
        'suffix' => ['required','string','max:5','regex:/^[A-Za-z\s\/\.\-]+$/'],
        'gender' => ['required','string','regex:/^[A-Za-z\s\-]+$/'],
        'civil_status' => ['required','string','regex:/^[A-Za-z\s\-]+$/'],
        'barangay' => ['required','string','regex:/^[A-Za-z\s\-]+$/'],
        'sitio' => ['required','string','max:255'],
        'birthdate' => ['required','date','before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
    ];
}


public function validateStepOne()
{
    try {
        $this->validate($this->userInfoRules());
        $this->dispatch('step-one-validated', success: true);
    } catch (\Illuminate\Validation\ValidationException $e) {
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
        $this->dispatch('step-two-validated', success: false);
        throw $e;
    }
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


  public function register()
{

    try {

        $validated = $this->validate();

        // Format contact
        $validated['contact'] = '+63' . $validated['contact'];

        // Handle profile picture upload
        $profilePath = null;
        if ($this->profile_pic instanceof TemporaryUploadedFile) {
            $profilePath = $this->profile_pic->store('profile-pics', 'public');
        }

        // Create User account
        $user = User::create([
            'name' => ucwords(strtolower(trim($validated['name']))),
            'email' => strtolower(trim($validated['email'])),
            'profile_pic' => $profilePath,
            'password' => Hash::make($validated['password']),
            'contact' => trim($validated['contact']),
            'agreed_terms' => true,
            'agreed_at' => now(),
        ]);

        // Assign "citizen" role
        $user->assignRole('citizen');

        // Create associated UserInfo
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
        \Log::error('Registration failed: '.$e->getMessage());
        $this->addError('register', 'Something went wrong. Please try again.');
    }

    // This will always run if no exception stops the flow
    $this->dispatch('register-finished');

    session()->put('just_registered', true);

    return $this->redirectIntended(route('dashboard'), navigate: true);
}



    public function render()
    {
        return view('livewire.pages.auth.register');
    }
}
