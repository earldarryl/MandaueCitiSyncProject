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
use Illuminate\Support\Str;

use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Schema;

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
    public $profilePicPreview = null;
    public bool $showMyModal = false;
    public bool $showProfileEditModal = false;
    public array $data = [];
    public string $originalName = '';
    public string $originalEmail = '';
    public $authUser;
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

    public function updatedDataProfilePic($value)
    {
        $this->profilePicPreview = $value ? $this->form->getState()['profile_pic']->temporaryUrl() : null;
    }

    public function mount(): void
    {
        $user = Auth::user();

        $this->authUser = $user;
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->current_profile_pic = $user->profile_pic;

        $this->originalName = $this->name;
        $this->originalEmail = $this->email;

    }

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

        $this->showProfileEditModal = false;
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
        $trigger = Str::uuid()->toString();
        session(['email_verify_trigger' => $trigger]);

        return $this->redirect(route('verification.notice', ['trigger' => $trigger]), navigate: true);
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
            ->title('Password updated')
            ->success()
            ->send();
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'delete_password' => ['required', 'string', 'current_password'],
        ]);

        $user = Auth::user();

        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        $user->profile_pic = null;

        tap($user, $logout(...))->delete();

        Notification::make()
            ->title('Account deactivated')
            ->danger()
            ->send();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}
