<div class="w-full">
    <div class="flex justify-center items-center py-3 text-center">
        <h1 class="text-2xl sm:text-3xl md:text-3xl lg:text-6xl oswald-font font-extrabold text-mc_primary_color dark:text-blue-600 drop-shadow-lg tracking-wide">
            {{ $pageTitle ?? 'CitiSync' }}
        </h1>
    </div>

    <div class="my-4 text-sm text-justify bg-gray-200 dark:bg-zinc-800 p-5 rounded-md">
        {{ __("Kindly, Please don't leave the page without completing on resetting your password.") }}
    </div>
    @if (session('validation_error'))
    <div class="text-red-600 bg-red-100 border border-red-400 p-3 rounded mb-4">
        {{ session('validation_error') }}
    </div>
    @endif
    <form wire:submit.prevent="resetPassword" class="flex flex-col gap-3">
        <flux:field class="flex flex-col gap-2">
            <flux:label>Email</flux:label>
                <flux:input.group>
                    <flux:input.group.prefix>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25" />
                        </svg>
                    </flux:input.group.prefix>

                    <flux:input wire:model.defer="email" id="email" type="email" name="email" autocomplete="username" disabled />
                </flux:input.group>

                <flux:error name="email" />
        </flux:field>

        <flux:field class="flex flex-col gap-2">
            <flux:label>Password</flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </flux:input.group.prefix>

                <flux:input wire:model.defer="password" id="password" type="password" name="password" class:input="hide-password-toggle" viewable autofocus autocomplete="new-password" clearable />
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

                <flux:input wire:model.defer="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" class:input="hide-password-toggle" viewable autocomplete="new-password" clearable />
            </flux:input.group>

                <flux:error name="password_confirmation" />
        </flux:field>

        <div class="flex items-center justify-end mt-4 gap-3">
            <x-primary-button wire:click="resetPassword" type="submit" class="border-2 border-black dark:border-white hover:bg-mc_primary_color dark:hover:bg-blue-600 hover:border-mc_primary_color dark:hover:border-blue-600 transition duration-300 ease-in-out">
                {{ __('Reset Password') }}
            </x-primary-button>

            <x-primary-button wire:click="abortConfirmation" type="button" class="border-2 border-black dark:border-white hover:bg-red-600 dark:hover:bg-red-600 hover:border-red-600 dark:hover:border-red-600 transition duration-300 ease-in-out">
                {{ _('Abort Confirmation')}}
            </x-primary-button>
        </div>
    </form>
    <script>
    window.addEventListener('store-reset-lock', event => {
        sessionStorage.setItem('reset_password_lock', event.detail.lock);
        sessionStorage.setItem('reset_password_token', event.detail.token);
        sessionStorage.setItem('reset_password_email', event.detail.email);
    });
</script>
<script>
    window.addEventListener('clear-reset-lock', () => {
        sessionStorage.removeItem('reset_password_lock');
        sessionStorage.removeItem('reset_password_token');
        sessionStorage.removeItem('reset_password_email');
    });
</script>
</div>
