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
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?string $current_profile_pic = null;
    public ?TemporaryUploadedFile $profile_pic = null;
    public string $current_password = '';
    public string $password = '';
    public string $delete_password = '';
    public string $password_confirmation = '';
    public $showMyModal = true;

    #[Validate(['required', 'image', 'max:2048'])]
    public $cropped_profile_pic;

    // Track original values
    public string $originalName = '';
    public string $originalEmail = '';

    protected $listeners = ['reset-form' => 'resetForm'];

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->current_profile_pic = $user->profile_pic;

        // Save originals for dirty/clean checks
        $this->originalName = $user->name;
        $this->originalEmail = $user->email;
    }

    public function isDirty(string $field): bool
    {
        return match ($field) {
            'name'  => $this->name !== $this->originalName,
            'email' => $this->email !== $this->originalEmail,
            default => false,
        };
    }

    public function isClean(string $field): bool
    {
        return !$this->isDirty($field);
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

        // Only run if something is dirty
        if ($this->isClean('name') && $this->isClean('email') && !$this->profile_pic) {
            Notification::make()
                ->title('No changes detected')
                ->warning()
                ->send();
            return;
        }

        $validated = $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        if ($this->profile_pic) {
            if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
                Storage::disk('public')->delete($user->profile_pic);
            }
            $validated['profile_pic'] = $this->profile_pic->store('profile_pics', 'public');
        } else {
            $validated['profile_pic'] = $user->profile_pic;
        }

        $user->update($validated);

        // Refresh originals so next change can be tracked
        $this->originalName = $this->name;
        $this->originalEmail = $this->email;
        $this->current_profile_pic = $user->profile_pic;
        $this->profile_pic = null;

        Notification::make()
            ->title('Profile updated successfully ✅')
            ->success()
            ->send();
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

        if (Auth::user()->profile_pic && Storage::disk('public')->exists(Auth::user()->profile_pic)) {
            Storage::disk('public')->delete(Auth::user()->profile_pic);
        }

        tap(Auth::user(), $logout(...))->delete();

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
