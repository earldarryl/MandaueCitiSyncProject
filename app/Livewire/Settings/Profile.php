<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Livewire\Actions\Logout;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Notifications\Notification;

#[Layout('layouts.app')]
class Profile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $password = '';
    public string $delete_password = '';
    public string $password_confirmation = '';
    public $showMyModal = true;

    protected $listeners = ['reset-form' => 'resetForm'];

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function resetForm()
    {
        $this->reset('current_password', 'password', 'password_confirmation');
        $this->resetErrorBag();

        $this->dispatch('reset-finished');
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
        ]);

        $user->fill($validated);

        if ($user->isClean()) {
            $this->addError('form', 'No changes were made.');
            return;
        }

        if ($user->isDirty('name')) {
            $this->dispatch('field-updated', field: 'name');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $this->dispatch('field-updated', field: 'email');
        }

        $user->save();

        $this->dispatch('user-profile-updated', [
            'name' => $user->name,
            'email' => $user->email,
        ])->to('partials.navigation');

        Notification::make()
            ->title('Welcome back, ' . $user->name . ' 👋')
            ->body('Good to see you again! Here’s your dashboard.')
            ->success()
            ->send()
            ->sendToDatabase($user);

        $this->dispatch('notification-created');
    }

    public function redirectEmailVerify()
    {
        $this->redirect(route('verification.notice'), navigate: true);
    }

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password', 'min:8'],
                'password' => ['required', 'string', 'min:8', Password::defaults(), 'confirmed'],
                'password_confirmation' => ['required', 'same:password'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        // ✅ Filament notification
        Notification::make()
            ->title('Password updated ✅')
            ->success()
            ->send();

        $this->dispatch('password-updated');
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'delete_password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        // ✅ Filament notification
        Notification::make()
            ->title('Account deactivated 🚫')
            ->danger()
            ->send();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}
