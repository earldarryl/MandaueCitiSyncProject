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
    public ?string $contact = null;
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

        $this->name  = $this->originalName;
        $this->email = $this->originalEmail;
        $this->contact = $this->contact ?? '';

        $this->form->fill([
            'profile_pic' => $user->profile_pic,
        ]);

        $this->dispatch('reset-finished');
    }

    public function updatedDataProfilePic($value)
    {
        $this->profilePicPreview = $value ? $this->form->getState()['profile_pic']->temporaryUrl() : null;
    }

    public function isDirty(string $field): bool
    {
        return match ($field) {
            'name'  => $this->name !== $this->originalName,
            'email' => $this->email !== $this->originalEmail,
            'contact' => $this->contact !== $this->formatPhoneForDisplay($this->authUser->contact ?? ''),
            default => false,
        };
    }


    public function isClean(string $field): bool
    {
        return !$this->isDirty($field);
    }

   private function formatPhoneForDisplay($number)
    {
        if (!$number) return '';

        $number = preg_replace('/\D/', '', $number);

        if (str_starts_with($number, '63')) {
            $number = substr($number, 2);
        } elseif (str_starts_with($number, '0')) {
            $number = substr($number, 1);
        }

        if (strlen($number) === 9) {
            $number = '9' . $number;
        }

        if (strlen($number) > 10) {
            $number = substr($number, -10);
        }

        if (strlen($number) !== 10) return '';

        return substr($number, 0, 3) . '-' .
            substr($number, 3, 3) . '-' .
            substr($number, 6, 4);
    }

    private function sanitizePhoneForSaving($value)
    {
        if (!$value) return null;

        $digits = preg_replace('/\D/', '', $value);
        $digits = ltrim($digits, '0');

        return '+63' . $digits;
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

        $rawContact = $user->contact ?? '';
        $this->contact = $this->formatPhoneForDisplay($rawContact);

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

    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $userInfo = $user->userInfo;

        $fieldsChanged =
            $this->isClean('name') &&
            $this->isClean('email') &&
            $this->isClean('contact');

        if ($fieldsChanged) {
            Notification::make()
                ->title('No changes detected')
                ->warning()
                ->send();
            return;
        }

        $validatedUser = $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'contact' => ['required', 'regex:/^(\+?\d{1,3})?\d{7,11}$|^\d{3}-\d{3}-\d{4}$/'],
        ]);

        $validatedUser['contact'] = $this->sanitizePhoneForSaving($this->contact);

        if ($user->hasRole('citizen')) {
            $validatedUserInfo['phone_number'] = $validatedUser['contact'];
        }

        if ($user->email !== $validatedUser['email']) {
            $user->email_verified_at = null;
        }

        $user->update($validatedUser);

        if ($user->hasRole('citizen')) {
            if ($userInfo) {
                $userInfo->update($validatedUserInfo);
            } else {
                $user->userInfo()->create($validatedUserInfo);
            }
        }

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
