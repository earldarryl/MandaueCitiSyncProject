<div class="h-full flex-1">
        <header class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-4xl font-bold flex items-center gap-3 text-mc_primary_color dark:text-white">
                <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span>
                    Profile
                </span>
            </h1>
            <p class="mt-2 text-md font-semibold text-justify text-body">
                Manage your personal details by updating your profile information, securing your account through password changes, or deactivating your account if needed.
            </p>
        </header>
        <div class="relative flex flex-col gap-10">
            <div class="relative shadow-md p-3">
                <header>
                    <h2 class="text-lg font-bold">{{ __('Profile Information') }}</h2>
                    <p class="mt-1 text-sm font-semibold">{{ __("Update your account's profile information and email address.") }}</p>
                </header>

                <form wire:submit.prevent="saveProfilePic" class="flex flex-col w-full mt-6 space-y-6">
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2 justify-start items-start">
                            <flux:label class="flex gap-2">
                                <flux:icon.camera class="text-mc_primary_color dark:text-white"/>
                                <span>Profile Picture</span>
                            </flux:label>
                        </div>

                        <div class="flex flex-col items-center mb-8" x-data="{ zoomSrc: null }">
                            @php
                                $palette = [
                                    '0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899',
                                    '14B8A6','6366F1','F97316','84CC16',
                                ];

                                $index = crc32($name) % count($palette);
                                $bgColor = $palette[$index];

                            @endphp

                            <div class="relative w-48 h-48 mb-1 group cursor-pointer" @click="$wire.set('showProfileEditModal', true)">

                                    <img
                                        src="{{ $profilePicPreview ?? ($current_profile_pic ? Storage::url($current_profile_pic) : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=' . $bgColor . '&color=fff&size=128') }}"
                                        alt="Profile Picture"
                                        class="rounded-full w-48 h-48 object-cover border-4 border-blue-500 shadow-md transition-transform duration-300 group-hover:scale-105"
                                    />

                                <div
                                    class="absolute inset-0 bg-black/50 rounded-full flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                >
                                    <x-heroicon-o-camera class="w-5 h-5 text-white"/>
                                    <span class="text-white text-sm font-semibold">Change Photo</span>
                                </div>
                            </div>

                        </div>

                        <div x-data="{ open: @entangle('showProfileEditModal') }" x-cloak>
                            <div
                                x-show="open"
                                x-transition.opacity
                                class="fixed inset-0 bg-black/50 z-40"
                                @click="open = false"
                            ></div>

                            <div
                                x-show="open"
                                x-transition.scale.origin.top
                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                            >
                                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg w-full max-w-3xl p-6 flex flex-col space-y-4">
                                    <div class="flex flex-col gap-3">
                                        <span class="text-center text-4xl font-bold text-mc_primary_color dark:text-blue-500">Edit Profile Picture</span>
                                        <span class="text-start text-md block">
                                            Choose or upload a new photo for your account.
                                        </span>
                                    </div>

                                    <div class="w-full flex items-center justify-center">
                                        {{ $this->form }}
                                    </div>

                                    <div class="flex flex-col items-center justify-center w-full gap-2">
                                        <flux:button
                                            type="submit"
                                            variant="primary"
                                            color="blue"
                                            class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                                            wire:target="saveProfilePic"
                                            wire:loading.attr="disabled"
                                            wire:loading.remove
                                        >
                                            <span>
                                                <span class="flex items-center justify-center gap-2">
                                                    <span><flux:icon.check variant="micro"/></span>
                                                    <span>{{ __('Save') }}</span>
                                                </span>
                                            </span>
                                        </flux:button>

                                        <flux:button
                                            variant="ghost"
                                            wire:click="$set('showProfileEditModal', false)"
                                            class="w-full border border-gray-600"
                                            wire:target="saveProfilePic"
                                            wire:loading.attr="disabled"
                                            wire:loading.remove
                                        >
                                            <span>
                                                <span class="flex items-center justify-center gap-2">
                                                    <span><flux:icon.x-mark variant="micro"/></span>
                                                    <span>{{ __('Cancel') }}</span>
                                                </span>
                                            </span>
                                        </flux:button>

                                        <div wire:loading wire:target="saveProfilePic">
                                            <div class="w-full flex items-center justify-center gap-2">
                                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <x-filament-actions::modals />

                <form wire:submit.prevent="updateProfileInformation"  class="mt-6 space-y-6">

                        <flux:field class="flex flex-col gap-2">
                            <div class="flex flex-col gap-2">
                                <flux:label class="flex gap-2">
                                    <flux:icon.tag class="text-mc_primary_color dark:text-white"/>
                                    <span>Name</span>
                                </flux:label>

                                <flux:input.group>
                                    <flux:input wire:model="name" id="name" type="text" name="name" autocomplete="name" />
                                </flux:input.group>

                            </div>

                            <flux:error name="name" />
                        </flux:field>

                        <flux:field class="flex flex-col gap-2">
                            <div class="flex flex-col gap-2">
                                <flux:label class="flex gap-2">
                                    <flux:icon.at-symbol class="text-mc_primary_color dark:text-white"/>
                                    <span>Email</span>
                                </flux:label>

                                <flux:input.group>
                                    <flux:input wire:model="email" id="email" type="text" name="email" autocomplete="email" />
                                </flux:input.group>

                            </div>

                            <flux:error name="email" />

                            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                                <flux:modal wire:model.self="showMyModal" name="my-unique-modal-name" class="w-full" :closable="false" :dismissible="false">
                                    <div class="flex flex-col items-center justify-center w-full">
                                        <div class="relative">
                                            <img
                                                src="{{ asset('/images/exclamation-mark.png') }}"
                                                class="w-full h-48 sm:h-56 object-cover rounded-t-xl"
                                                alt="Heads up! Background"
                                            >
                                        </div>

                                        <div class="flex flex-col items-center space-y-4 w-full px-4 py-6">
                                            <span class="text-5xl font-extrabold text-red-600 text-center">
                                                Heads Up!
                                            </span>

                                            <span class="text-lg font-bold text-center text-gray-800 dark:text-gray-200">
                                                Your email has been updated.
                                            </span>

                                            <span class="text-sm text-center font-semibold text-gray-600 dark:text-gray-400 leading-relaxed">
                                                Please verify your email to continue using your account. Click the button below to proceed to verification.
                                            </span>
                                            <div class="flex flex-col gap-2 items-center justify-center" x-data="{ clicked: @entangle('emailVerifyClicked') }">
                                                <div wire:loading.remove wire:target="redirectEmailVerify" class="mt-4">
                                                    <flux:button
                                                        type="button"
                                                        variant="ghost"
                                                        wire:click="redirectEmailVerify"
                                                        x-bind:disabled="clicked"
                                                        class="w-full border border-gray-400 dark:border-zinc-700"
                                                    >
                                                        <span wire:loading.remove wire:target="redirectEmailVerify">Proceed to Verify</span>
                                                        <span wire:loading wire:target="redirectEmailVerify">Redirecting...</span>
                                                    </flux:button>
                                                </div>

                                                <div wire:loading wire:target="redirectEmailVerify">
                                                    <div class="flex items-center justify-center gap-2 mt-4">
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </flux:modal>
                            @endif
                        </flux:field>

                        <flux:field>
                            <div class="flex flex-col gap-2">
                                <flux:label class="flex gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                    </svg>
                                    <span>Contact Number</span>
                                </flux:label>

                                <flux:input.group
                                    x-data="{
                                            formatPhone(event) {
                                                let value = event.target.value.replace(/\D/g, '');
                                                if (value.length > 10) value = value.slice(0, 10);
                                                if (value.length > 6)
                                                    event.target.value = `${value.slice(0,3)}-${value.slice(3,6)}-${value.slice(6,10)}`;
                                                else if (value.length > 3)
                                                    event.target.value = `${value.slice(0,3)}-${value.slice(3,6)}`;
                                                else event.target.value = value;
                                            }
                                        }"
                                >
                                    <flux:input.group.prefix class="flex gap-2 justify-center items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 72 72">
                                            <path fill="#1e50a0" d="M5 17h62v38H5z" />
                                            <path fill="#d22f27" d="M5 36h62v19H5z" />
                                            <path fill="#fff" d="M37 36L5 55V17z" />
                                        </svg>
                                        <span>+63</span>
                                    </flux:input.group.prefix>

                                    <flux:input
                                        wire:model="contact"
                                        type="text"
                                        maxlength="12"
                                        inputmode="numeric"
                                        placeholder="917-456-7890"
                                        autocomplete="tel"
                                        x-on:input="formatPhone($event)"
                                        clearable
                                    />
                                </flux:input.group>

                            </div>

                            <flux:error name="contact" />
                        </flux:field>

                        <div class="flex items-center justify-center w-full gap-4">
                            <flux:button
                                type="submit"
                                variant="primary"
                                color="blue"
                                class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                                wire:target="updateProfileInformation"
                                wire:loading.attr="disabled"
                                wire:loading.remove
                            >
                                <span>
                                    <span class="flex items-center justify-center gap-2">
                                        <span><flux:icon.check variant="micro"/></span>
                                        <span>{{ __('Save') }}</span>
                                    </span>
                                </span>
                            </flux:button>
                            <div wire:loading wire:target="updateProfileInformation">
                                <div class="w-full flex items-center justify-center gap-2">
                                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>

        <div class="relative shadow-md p-3">

            <header>
                <h2 class="text-lg font-bold">
                    {{ __('Update Password') }}
                </h2>

                <p class="mt-1 text-sm font-semibold">
                    {{ __('Ensure your account is using a long, random password to stay secure.') }}
                </p>
            </header>

            <form wire:submit="updatePassword" class="mt-6 space-y-6">

                <flux:field class="flex flex-col gap-2">
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.lock-closed class="text-mc_primary_color dark:text-white"/>
                            <span>Current Password</span>
                        </flux:label>

                        <flux:input.group>
                            <flux:input
                                wire:model="current_password"
                                id="update_password_current_password"
                                type="password"
                                placeholder="Enter your current password"
                                viewable
                                clearable/>
                        </flux:input.group>

                    </div>

                    <flux:error name="current_password" />
                </flux:field>

                <flux:field class="flex flex-col gap-2">
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.arrow-path class="text-mc_primary_color dark:text-white"/>
                            <span>New Password</span>
                        </flux:label>

                        <flux:input.group>
                            <flux:input
                                wire:model="password"
                                id="update_password_password"
                                type="password"
                                placeholder="Enter your new password"
                                viewable
                                clearable/>
                        </flux:input.group>

                    </div>

                    <flux:error name="password" />
                </flux:field>

                <flux:field class="flex flex-col gap-2">
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.shield-check class="text-mc_primary_color dark:text-white"/>
                            <span>Confirm Password</span>
                        </flux:label>

                        <flux:input.group>
                            <flux:input
                                wire:model="password_confirmation"
                                id="update_password_password_confirmation"
                                type="password"
                                placeholder="Confirm your password"
                                viewable
                                clearable/>
                        </flux:input.group>

                    </div>

                    <flux:error name="password_confirmation" />
                </flux:field>

                <div class="flex items-center justify-center w-full gap-4">
                    <flux:button
                        type="submit"
                        variant="primary"
                        color="blue"
                        class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                        wire:target="updatePassword"
                        wire:loading.attr="disabled"
                        wire:loading.remove
                    >
                        <span>
                            <span class="flex items-center justify-center gap-2">
                                <span><flux:icon.check variant="micro"/></span>
                                <span>{{ __('Save') }}</span>
                            </span>
                        </span>
                    </flux:button>
                    <div wire:loading wire:target="updatePassword">
                        <div class="w-full flex items-center justify-center gap-2">
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="relative shadow-md p-3">
            <header>
                <h2 class="text-lg font-bold">
                    {{ __('Delete Account') }}
                </h2>

                <p class="mt-1 text-sm font-semibold">
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

                    <flux:field class="flex flex-col gap-2">
                        <div class="flex flex-col gap-2">
                            <flux:label class="flex gap-2">
                                <flux:icon.tag class="text-mc_primary_color dark:text-white"/>
                                <span>Password</span>
                            </flux:label>

                            <flux:input.group>
                                <flux:input
                                    wire:model="delete_password"
                                    id="delete_password"
                                    name="delete_password"
                                    type="password"
                                    placeholder="Enter your password"
                                    viewable
                                    clearable/>
                            </flux:input.group>

                        </div>

                        <flux:error name="delete_password" />
                    </flux:field>

                    <div class="mt-6 flex justify-center gap-4">
                        <flux:button
                            class="cursor-pointer"
                            variant="primary"
                            icon="x-mark"
                            x-on:click="$dispatch('close')"
                            wire:target="deleteUser"
                            wire:loading.remove
                        >
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button
                            class="cursor-pointer"
                            icon="trash"
                            variant="danger"
                            wire:click="deleteUser"
                            wire:target="deleteUser"
                            wire:loading.remove
                        >
                            {{ __('Delete Account') }}
                        </flux:button>
                        <div wire:loading wire:target="deleteUser">
                            <div class="w-full flex items-center justify-center gap-2">
                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </x-modal>
        </div>
    </div>
</div>

