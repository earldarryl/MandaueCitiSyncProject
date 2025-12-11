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
    public bool $showMyModal = true;
    public bool $showProfileEditModal = false;
    public bool $emailVerifyClicked = false;
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
                    ->directory('avatars')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->required()
                    ->alignCenter()
                    ->validationMessages([
                        'required' => '<div class="flex items-center justify-start gap-2 mt-3 text-sm font-medium text-red-500 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            class="w-5 h-5 flex-shrink-0">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.596c.75 1.335-.213 3.05-1.742 3.05H3.48c-1.53 0-2.492-1.715-1.741-3.05L8.257 3.1zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-4a.75.75 0 00-.75.75v2.5c0 .414.336.75.75.75s.75-.336.75-.75v-2.5A.75.75 0 0010 9z"
                                clip-rule="evenodd" />
                        </svg>

                        <span>Please select and upload a profile picture before saving.</span>
                    </div>
                    ',
                    ])
                    ->allowHtmlValidationMessages()
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

            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No image uploaded',
                'message' => 'Please select and upload a profile picture before saving.',
            ]);


            return;
        }

        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        $user->update([
            'profile_pic' => $path,
        ]);

        $this->current_profile_pic = $path;

        $state['profile_pic'] = null;

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Profile picture updated!',
            'message' => '',
        ]);

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

            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No changes detected',
                'message' => 'Please make any changes to your credentials first.',
            ]);
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

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Profile updated successfully!',
            'message' => '',
        ]);
    }

    public function redirectEmailVerify()
    {
        if ($this->emailVerifyClicked) {
            return;
        }

        $this->emailVerifyClicked = true;

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

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Password updated',
            'message' => '',
        ]);
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

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Account deactivated',
            'message' => '',
        ]);

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}
