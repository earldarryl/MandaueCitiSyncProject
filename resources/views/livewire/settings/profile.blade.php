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
                <h2 class="text-lg font-medium">
                    {{ __('Profile Information') }}
                </h2>

                <p class="mt-1 text-sm">
                    {{ __("Update your account's profile information and email address.") }}
                </p>
            </header>

                <form wire:submit.prevent="saveProfilePic" enctype="multipart/form-data" class="mt-6 space-y-6">
                    <flux:error name="form" />

                    {{-- Profile Picture Upload with Cropper --}}
                        <div x-data="{
                            preview: @js($current_profile_pic ? Storage::url($current_profile_pic) : asset('images/avatar.png')),
                            showModal: false,
                            cropper: null,

                            handleChange(e) {
                                const file = e.target.files?.[0];
                                if (!file) return;

                                const reader = new FileReader();
                                reader.onload = (event) => {
                                    this.preview = event.target.result;
                                    this.showModal = true;

                                    this.$nextTick(() => {
                                        const image = this.$refs.cropImage;
                                        if (this.cropper) this.cropper.destroy();

                                        this.cropper = new Cropper(image, {
                                            aspectRatio: 1,
                                            viewMode: 1,
                                            autoCropArea: 1,
                                            responsive: true,
                                        });
                                    });
                                };
                                reader.readAsDataURL(file);
                            },

                            cropAndSave() {
                            if (!this.cropper) return;

                            this.cropper.getCroppedCanvas({ width: 400, height: 400 }).toBlob((blob) => {
                                const file = new File([blob], 'profile.jpg', { type: 'image/jpeg' });

                                // Close modal immediately
                                this.showModal = false;
                                this.cropper.destroy();
                                this.cropper = null;

                                // Update preview immediately
                                this.preview = URL.createObjectURL(blob);

                                // Upload to Livewire in the background
                                $wire.upload('profile_pic', file, {
                                    finish: () => {
                                        // Optionally, save profile immediately
                                        $wire.call('saveProfilePic');
                                    },
                                    error: (err) => console.error(err),
                                });
                            });
                        },

                            cancelCrop() {
                                if (this.cropper) {
                                    this.cropper.destroy();
                                    this.cropper = null;
                                }
                                this.showModal = false;
                                this.preview = @js($current_profile_pic ? Storage::url($current_profile_pic) : asset('images/avatar.png'));
                            }
                        }" class="flex flex-col items-center gap-4">

                            <!-- Avatar -->
                            <div wire:ignore>
                            <div class="relative w-64 h-64 rounded-full overflow-hidden cursor-pointer group">
                                <img :src="preview" class="w-full h-full object-cover" alt="Profile">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 group-hover:opacity-100 transition">
                                    <span class="text-white text-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-8">
                                            <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                            <path fill-rule="evenodd" d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>

                                <input type="file" @change="handleChange($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                            </div>

                            <!-- Cropper Modal -->
                            <div
                                x-show="showModal"
                                x-transition.opacity
                                style="display:none"
                                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50"
                                wire:ignore
                            >
                                <div
                                    class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-95"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                >
                                    <!-- Header -->
                                    <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                                        <h2 class="text-lg font-semibold text-gray-800">Crop Your Image</h2>
                                        <button
                                            type="button"
                                            @click="cancelCrop()"
                                            class="text-gray-400 hover:text-gray-600 transition"
                                        >
                                            ✕
                                        </button>
                                    </div>

                                    <!-- Body -->
                                    <div class="p-6 flex justify-center">
                                        <img
                                            x-ref="cropImage"
                                            :src="preview"
                                            class="max-w-full max-h-[400px] rounded-lg border border-gray-200 shadow-sm"
                                            alt="Crop"
                                        >
                                    </div>

                                    <!-- Footer -->
                                    <div class="flex justify-end gap-3 px-6 py-4 bg-gray-50 border-t">
                                        <flux:button
                                            variant="primary"
                                            icon="x-mark"
                                            type="button"
                                            @click="cancelCrop()"
                                            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 transition"
                                        >
                                            Cancel
                                        </flux:button>
                                        <flux:button
                                            variant="primary"
                                            icon="scissors"
                                            color="blue"
                                            type="button"
                                            @click="cropAndSave()"
                                            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-md transition"
                                        >
                                            Crop & Save
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                <flux:error name="profile_pic" />
            </form>

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

