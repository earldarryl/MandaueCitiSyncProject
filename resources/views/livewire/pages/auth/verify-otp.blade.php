<div class="mb-4 text-sm w-full text-justify">
    <div class="flex justify-center items-center py-3 text-center">
        <h1 class="text-6xl oswald-font font-extrabold text-mc_primary_color dark:text-blue-600 drop-shadow-lg tracking-wide">
            {{ $title }}
        </h1>
    </div>

    <div class="my-4 text-sm text-justify indent-4 bg-gray-200 dark:bg-zinc-800 p-5 rounded-md">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if ($status === 'verification-link-sent')
        <div class="flex gap-3 mb-4 font-medium text-sm text-green-400"
             x-data="{ showStatus: true }"
             x-init="setTimeout(() => showStatus = false, 5000)"
             x-show="showStatus"
             x-transition:leave.duration.500ms
             role="alert"
        >
            <x-heroicon-o-check class="w-5 h-5" />
            <span>
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </span>
        </div>
    @endif

    @if ($textSuccessMessage)
        <div class="flex gap-3 mb-4 font-medium text-sm text-green-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <x-heroicon-o-check class="w-5 h-5" />
            <flux:text>{{ $textSuccessMessage }}</flux:text>
        </div>
    @endif

    <div class="flex flex-col w-full h-full gap-4 p-4">

        <flux:field class="flex flex-col gap-2">
            <flux:label class="flex gap-2">
                    <flux:icon.device-phone-mobile/>
                    <span>Enter OTP</span>
                </flux:label>
            <flux:input id="otp" name="otp" type="text" wire:model.defer="otp" class="mt-1 w-full" autofocus x-on:keydown.enter.prevent="$wire.call('verifyOtp')" />
            <flux:error name="otp" />
        </flux:field>

        <div wire:target="verifyOtp" wire:loading.remove>
            <div class="w-full flex justify-center items-center">
                <flux:button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="verifyOtp"
                    wire:click="verifyOtp"
                    variant="primary"
                    color="blue"
                    icon="check"
                    class="w-full group bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                >
                    <span>{{ __('Verify OTP') }}</span>
                </flux:button>
            </div>
        </div>

        <div wire:target="verifyOtp" wire:loading.remove>
            <div class="w-full flex justify-end gap-2">
                <div
                    x-data="{
                        timeLeft: @entangle('cooldown').defer ?? 0,
                        label: '',
                        startCooldown(seconds = 30) {
                            this.timeLeft = seconds;
                            this.updateLabel();
                            const interval = setInterval(() => {
                                if (this.timeLeft > 1) {
                                    this.timeLeft--;
                                    this.updateLabel();
                                } else {
                                    clearInterval(interval);
                                    this.timeLeft = 0;
                                    this.updateLabel();
                                }
                            }, 1000);
                        },
                        updateLabel() {
                            this.label = this.timeLeft > 0 ? 'Wait ' + this.timeLeft + 's' : 'Resend OTP';
                        },
                        resend() {
                            if (this.timeLeft > 0) return;
                            $wire.sendVerification();
                            this.startCooldown(60);
                        }
                    }"
                    x-init="
                        if (timeLeft > 0) { startCooldown(timeLeft); } else { updateLabel(); }
                    "
                    class="w-full"
                >
                    <flux:button
                        type="button"
                        variant="primary"
                        color="blue"
                        icon="cursor-arrow-ripple"
                        class="w-full group transition duration-300 ease-in-out
                            disabled:cursor-not-allowed disabled:opacity-50
                            bg-mc_primary_color dark:bg-blue-700"
                        @click="resend()"
                        x-bind:disabled="timeLeft > 0"
                        wire:loading.attr="disabled"
                        wire:target="sendVerification"
                    >
                        <span wire:loading.remove wire:target="sendVerification" x-text="label"></span>
                        <span wire:loading wire:target="sendVerification">Sending...</span>
                    </flux:button>
                </div>

            <flux:button
                type="button"
                wire:click="logout"
                variant="danger"
                color="rose"
                wire:loading.attr="disabled"
                wire:target="logout"
                icon="arrow-left-end-on-rectangle"
                class="w-full group bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="logout">Log Out</span>
                <span wire:loading wire:target="logout">Logging Out...</span>
            </flux:button>
            </div>
        </div>

        <div wire:target="verifyOtp" wire:loading>
            <div class="w-full flex items-center justify-center gap-2">
                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
            </div>
        </div>
    </div>
</div>
