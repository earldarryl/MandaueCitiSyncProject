<div class="h-full flex-1 overflow-y-auto p-4">
    {{-- Header --}}
        <header class="pb-6 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-4xl font-bold flex items-center gap-3 text-gray-900 dark:text-gray-100">
                <svg class="w-10 h-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Profile
            </h1>
        </header>
        <div class="relative flex flex-col gap-10">
            <div class="relative shadow-md p-3">
                <header>
                    <h2 class="text-lg font-medium">{{ __('Profile Information') }}</h2>
                    <p class="mt-1 text-sm">{{ __("Update your account's profile information and email address.") }}</p>
                </header>

                {{-- Filament FileUpload form for profile picture --}}
                <form wire:submit.prevent="saveProfilePic" class="mt-6 space-y-6">
                    {{-- renders the FileUpload component (image editor modal is produced by Filament) --}}
                    {{ $this->form }}

                    <div class="flex items-center gap-4">
                        <flux:button
                            type="submit"
                            variant="primary"
                            color="blue"
                            class="w-full group bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                            wire:target="saveProfilePic"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading wire:target="saveProfilePic">
                                <span class="flex items-center justify-center gap-2">
                                    <span><flux:icon.loading variant="micro"/></span>
                                    <span>{{ __('Saving....') }}</span>
                                </span>
                            </span>
                            <span wire:loading.remove wire:target="saveProfilePic">
                                <span class="flex items-center justify-center gap-2">
                                    <span><flux:icon.arrow-right-circle variant="micro"/></span>
                                    <span>{{ __('Save') }}</span>
                                </span>
                            </span>
                        </flux:button>
                    </div>
                </form>

                {{-- It's IMPORTANT to render Filament modals somewhere inside this Livewire component --}}
                <x-filament-actions::modals />

                <form wire:submit.prevent="updateProfileInformation"  class="mt-6 space-y-6">
                    {{-- Name Field --}}
                        <flux:field
                            x-data="{ status: '', timeout: null }"
                            x-init="
                                $wire.$on('field-updated', e => {
                                    if (e.field === 'name') {
                                        clearTimeout(timeout);
                                        status = 'success';
                                        timeout = setTimeout(() => status = '', 2000);
                                    }
                                });
                            "
                            class="flex flex-col gap-3"
                        >
                            <flux:label for="name">{{ __('Name') }}</flux:label>
                            <flux:input.group>
                                <flux:input.group.prefix>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                    </svg>
                                </flux:input.group.prefix>
                                <flux:input
                                    wire:model="name"
                                    id="name"
                                    name="name"
                                    type="text"
                                    autocomplete="name"
                                    clearable
                                />
                            </flux:input.group>
                            <flux:error name="name" />
                        </flux:field>

                        {{-- Email Field --}}
                        <flux:field
                            x-data="{ status: '', timeout: null }"
                            x-init="
                                $wire.$on('field-updated', e => {
                                    if (e.field === 'email') {
                                        clearTimeout(timeout);
                                        status = 'success';
                                        timeout = setTimeout(() => status = '', 2000);
                                    }
                                });
                            "
                            class="flex flex-col gap-3"
                        >
                            <flux:label for="email">{{ __('Email') }}</flux:label>
                            <flux:input.group>
                                <flux:input.group.prefix>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                    </svg>
                                </flux:input.group.prefix>
                                <flux:input
                                    wire:model="email"
                                    id="email"
                                    name="email"
                                    type="text"
                                    autocomplete="email"
                                    clearable
                                />
                            </flux:input.group>
                            <flux:error name="email" />

                                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                                    <flux:modal wire:model.self="showMyModal" name="my-unique-modal-name" class="w-96">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg" class="flex gap-3 items-center">
                                                    <span class="inline-flex shrink-0 rounded-full border border-pink-300 bg-pink-100 p-2 dark:border-pink-300/10 dark:bg-pink-400/10">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 text-pink-700 dark:text-pink-500">
                                                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                    <span class="font-bold text-lg">Heads up!</span>
                                                </flux:heading>
                                                <flux:text class="mt-2">
                                                    Your email is updated, please verify your email.
                                                </flux:text>
                                            </div>
                                            <div class="flex">
                                                <flux:spacer />
                                                <flux:button type="button" variant="primary" wire:click="redirectEmailVerify">
                                                    Proceed
                                                </flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                @endif
                        </flux:field>

                        {{-- Save Button --}}
                        <div class="flex items-center gap-4">
                            <flux:button
                                type="submit"
                                variant="primary"
                                color="blue"
                                class="w-full group bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                                wire:target="updateProfileInformation"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading wire:target="updateProfileInformation">
                                    <span class="flex items-center justify-center gap-2">
                                        <span><flux:icon.loading variant="micro"/></span>
                                        <span>{{ __('Saving....') }}</span>
                                    </span>
                                </span>
                                <span wire:loading.remove wire:target="updateProfileInformation">
                                    <span class="flex items-center justify-center gap-2">
                                        <span><flux:icon.arrow-right-circle variant="micro"/></span>
                                        <span>{{ __('Save') }}</span>
                                    </span>
                                </span>
                            </flux:button>
                        </div>
                    </form>
            </div>



        <div class="relative shadow-md p-3">
        <header>
            <h2 class="text-lg font-medium">
                {{ __('Update Password') }}
            </h2>

            <p class="mt-1 text-sm">
                {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </p>
        </header>

        <form wire:submit="updatePassword" class="mt-6 space-y-6">

            <flux:field class="flex flex-col gap-2">
                <flux:label>Current Password</flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                        </svg>
                    </flux:input.group.prefix>

                    <flux:input
                        wire:model="current_password"
                        id="update_password_current_password"
                        type="password"
                        class:input="hide-password-toggle"
                        placeholder="Enter your current password"
                        viewable
                        clearable
                    />

                </flux:input.group>
                    <flux:error name="current_password" />
            </flux:field>

            <flux:field class="flex flex-col gap-2">
                <flux:label>New Password</flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </flux:input.group.prefix>

                    <flux:input
                        wire:model="password"
                        id="update_password_password"
                        type="password"
                        class:input="hide-password-toggle"
                        placeholder="Enter your new password"
                        viewable
                        clearable
                    />

                </flux:input.group>
                    <flux:error name="password" />
            </flux:field>


            <flux:field class="flex flex-col gap-2">
                <flux:label>Confirm Password</flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </flux:input.group.prefix>

                    <flux:input
                        wire:model="password_confirmation"
                        id="update_password_password_confirmation"
                        type="password"
                        class:input="hide-password-toggle"
                        placeholder="Confirm your password"
                        viewable
                        clearable
                    />

                </flux:input.group>
                    <flux:error name="password_confirmation" />
            </flux:field>

            <div class="flex items-center gap-4">
                <flux:button
                    variant="primary"
                    color="blue"
                    class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                    wire:click="updatePassword"
                    wire:loading.attr="disabled"
                    wire:loading.class="cursor-not-allowed"
                    >

                    <span wire:loading wire:target="updatePassword">
                        <span class="flex items-center justify-center gap-2">
                                <span>
                                <flux:icon.loading variant="micro"/>
                            </span>
                            <span>{{ __('Saving....') }}</span>
                        </span>
                    </span>
                    <span wire:loading.remove wire:target="updatePassword">
                        <span class="flex items-center justify-center gap-2">
                            <span>
                                <flux:icon.arrow-right-circle variant="micro"/>
                            </span>
                            <span>{{ __('Save') }} </span>
                        </span>
                    </span>
                </flux:button>
            </div>
        </form>
        </div>
        <div class="relative shadow-md p-3">
        <header>
            <h2 class="text-lg font-medium">
                {{ __('Delete Account') }}
            </h2>

            <p class="mt-1 text-sm">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
            </p>
        </header>

        <flux:button
                x-data
                icon="trash"
                variant="danger"
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="w-full p-3 my-4 cursor-pointer"
            >{{ __('Delete Account') }}
        </flux:button>

        <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
            <form wire:submit="deleteUser" class="p-6 flex flex-col gap-3">

                <h2 class="text-lg font-medium">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>

                <p class="mt-1 text-sm">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <flux:field class="flex flex-col gap-2 mt-3">
                    <flux:label>Password</flux:label>

                    <flux:input.group>
                        <flux:input.group.prefix>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                </svg>
                        </flux:input.group.prefix>

                            <flux:input
                                wire:model="delete_password"
                                id="delete_password"
                                name="delete_password"
                                type="password"
                                class:input="hide-password-toggle"
                                placeholder="Password"
                                viewable
                                clearable
                            />

                        </flux:input.group>
                        <flux:error name="delete_password" />
                </flux:field>

                <div class="mt-6 flex justify-center gap-4">
                <flux:button
                    class="cursor-pointer"
                    variant="primary"
                    icon="x-mark"
                    x-on:click="$dispatch('close')"
                >
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button
                    class="cursor-pointer"
                    icon="trash"
                    variant="danger"
                    wire:click="deleteUser"
                >
                    {{ __('Delete Account') }}
                </flux:button>
                </div>
            </form>
        </x-modal>
        </div>
    </div>
</div>

