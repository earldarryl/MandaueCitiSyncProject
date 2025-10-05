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
                    <flux:icon.at-symbol class="text-mc_primary_color dark:text-white"/>
                    <span class="text-lg font-bold">Email</span>
                </flux:label>

                <flux:input.group>
                    <flux:input wire:model.defer="email" id="email" type="email" name="email" class:input="font-semibold text-lg" autocomplete="email" disabled />
                </flux:input.group>

            </div>

            <flux:error name="email" />
        </flux:field>

        <flux:field class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <flux:label class="flex gap-2">
                    <flux:icon.lock-closed class="text-mc_primary_color dark:text-white"/>
                    <span class="text-lg font-bold">Password</span>
                </flux:label>

                <flux:input.group>
                    <flux:input wire:model.defer="password" id="password" type="password" name="password" class:input="hide-password-toggle font-semibold text-lg" viewable autofocus autocomplete="new-password" clearable />
                </flux:input.group>

            </div>

            <flux:error name="password" />
        </flux:field>

        <flux:field class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <flux:label class="flex gap-2">
                    <flux:icon.check-circle class="text-mc_primary_color dark:text-white"/>
                    <span class="text-lg font-bold">Confirm Password</span>
                </flux:label>

                <flux:input.group>
                    <flux:input wire:model.defer="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" class:input="hide-password-toggle font-semibold text-lg" viewable autocomplete="new-password" clearable />
                </flux:input.group>

            </div>

            <flux:error name="password_confirmation" />
        </flux:field>

        <div class="flex items-center justify-center mt-4 gap-3">
            <flux:button variant="primary"
                    color="blue"
                    class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                    wire:click="resetPassword"
                    wire:target="abortConfirmation, resetPassword"
                    wire:loading.remove
                    type="submit"
                    >

                    <span>
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
                    wire:target="abortConfirmation, resetPassword"
                    wire:loading.remove
                    >

                    <span>
                        <span class="flex items-center justify-center gap-2">
                            <span>
                                <flux:icon.x-mark variant="micro"/>
                            </span>
                            <span>{{ _('Abort Confirmation')}}</span>
                        </span>
                    </span>

            </flux:button>

            <div wire:loading wire:target="abortConfirmation, resetPassword">
                <div class="w-full flex items-center justify-center gap-2">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
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
