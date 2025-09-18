<div class="w-full">
    <div class="flex justify-center items-center py-3 text-center">
        <h1 class="text-6xl font-bold tracking-tighter text-mc_primary_color dark:text-blue-600 ">
            {{ $title }}
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
            <div class="flex flex-col gap-2">
                <flux:label class="flex gap-2">
                    <flux:icon.at-symbol />
                    <span>Email</span>
                </flux:label>

                <flux:input.group>
                    <flux:input wire:model.defer="email" id="email" type="email" name="email" autocomplete="username" disabled />
                </flux:input.group>

            </div>

            <flux:error name="email" />
        </flux:field>

        <flux:field class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <flux:label class="flex gap-2">
                    <flux:icon.lock-closed />
                    <span>Password</span>
                </flux:label>

                <flux:input.group>
                    <flux:input wire:model.defer="password" id="password" type="password" name="password" class:input="hide-password-toggle" viewable autofocus autocomplete="new-password" clearable />
                </flux:input.group>

            </div>

            <flux:error name="password" />
        </flux:field>

        <flux:field class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <flux:label class="flex gap-2">
                    <flux:icon.check-circle />
                    <span>Confirm Password</span>
                </flux:label>

                <flux:input.group>
                    <flux:input wire:model.defer="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" class:input="hide-password-toggle" viewable autocomplete="new-password" clearable />
                </flux:input.group>

            </div>

            <flux:error name="password_confirmation" />
        </flux:field>

        <div class="flex items-center justify-end mt-4 gap-3">
            <flux:button variant="primary"
                    color="blue"
                    class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                    wire:click="resetPassword"
                    type="submit">

                <span wire:loading wire:target="resetPassword">
                        <span class="flex items-center justify-center gap-2">
                             <span>
                                <flux:icon.loading variant="micro"/>
                            </span>
                        </span>
                    </span>
                    <span wire:loading.remove wire:target="resetPassword">
                        <span class="flex items-center justify-center gap-2">
                            <span>
                                <flux:icon.arrow-path variant="micro"/>
                            </span>
                            <span>{{ __('Reset Password') }}</span>
                        </span>
                    </span>
            </flux:button>

            <flux:button
                    variant="danger"
                    class="w-full"
                    wire:click="abortConfirmation"
                    type="button"
                    >
                    <span wire:loading wire:target="abortConfirmation">
                        <span class="flex items-center justify-center gap-2">
                             <span>
                                <flux:icon.loading variant="micro"/>
                            </span>
                        </span>
                    </span>
                    <span wire:loading.remove wire:target="abortConfirmation">
                        <span class="flex items-center justify-center gap-2">
                            <span>
                                <flux:icon.x-mark variant="micro"/>
                            </span>
                            <span>{{ _('Abort Confirmation')}}</span>
                        </span>
                    </span>
            </flux:button>
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
