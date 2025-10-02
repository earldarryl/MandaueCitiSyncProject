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
use Illuminate\Support\Facades\Storage;

// Filament schema-based APIs (v4+)
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Schema;

// Filament form field(s)
use Filament\Forms\Components\FileUpload;

#[Layout('layouts.app')]
class Profile extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $password = '';
    public string $delete_password = '';
    public string $password_confirmation = '';
    public ?string $current_profile_pic = null;
    public bool $showMyModal = false;

    // Filament form state (statePath('data') below)
    public array $data = [];

    // Track original values
    public string $originalName = '';
    public string $originalEmail = '';
    protected $listeners = ['reset-form' => 'resetForm'];
    public function resetForm(): void
    {
        $user = Auth::user();

        $this->reset([
            'current_password',
            'password',
            'password_confirmation',
            'delete_password',
        ]);

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';

        $this->originalName = $this->name;
        $this->originalEmail = $this->email;

        $this->form->fill([
            'profile_pic' => $user->profile_pic,
        ]);

        $this->dispatch('reset-finished');
    }


    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->current_profile_pic = $user->profile_pic;

        // original values for dirty checks
        $this->originalName = $this->name;
        $this->originalEmail = $this->email;

        // Initialize the Filament form
        $this->form->fill([
            'profile_pic' => $user->profile_pic,
        ]);
    }

    /**
     * NOTE: For Filament v4+ use Schema here.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('profile_pic')
                    ->avatar()
                    ->disk('public')
                    ->directory('profile_pics')
                    ->image()
                    ->imageEditor()
                    ->circleCropper()
                    ->maxSize(2048)
                    ->hiddenLabel(true)
            ])
            ->statePath('data');
    }

    public function saveProfilePic(): void
    {
        $user = Auth::user();

        $state = $this->form->getState();
        $path = $state['profile_pic'] ?? null;

        if (! $path) {
            Notification::make()
                ->title('No image uploaded')
                ->body('Please select and upload a profile picture before saving.')
                ->warning()
                ->send();

            return;
        }

        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        $user->update([
            'profile_pic' => $path,
        ]);

        $this->current_profile_pic = $path;

        $this->form->fill(['profile_pic' => $path]);

        Notification::make()
            ->title('Profile picture updated!')
            ->success()
            ->send();
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

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        if ($this->isClean('name') && $this->isClean('email')) {
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

        if ($user->email !== $validated['email']) {
            $user->email_verified_at = null;
        }

        $user->update($validated);

        $this->originalName = $this->name;
        $this->originalEmail = $this->email;

        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();
    }

    public function redirectEmailVerify()
    {
        return $this->redirect(route('verification.notice', ['trigger' => 1]), navigate: true);
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
