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
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
            role="alert"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex flex-col w-full h-full gap-4 p-4">
        <!-- OTP Verification Form -->
        <form wire:submit.prevent="verifyOtp" class="space-y-4 w-full">
            @csrf

            <flux:field class="flex flex-col gap-2">
                <flux:label>Enter OTP</flux:label>
                <flux:input id="otp" name="otp" type="text" wire:model.defer="otp" class="mt-1 w-full" autofocus />
                <flux:error name="otp" />

                @if (session('success'))
                    <flux:text variant="success">{{ session('success') }}</flux:text>
                @endif
            </flux:field>

            <div class="w-full flex justify-end">
                <flux:button
                    type="submit"
                    variant="primary"
                    color="blue"
                    class="w-full group bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="verifyOtp">{{ __('Verify OTP') }}</span>
                    <span wire:loading wire:target="verifyOtp">Verifying...</span>
                </flux:button>
            </div>
        </form>

        <div class="w-full flex justify-end gap-2 mt-4">
            <div
                x-data="{
                    timeLeft: @js($cooldown),
                    get label() {
                        return this.timeLeft > 0
                            ? 'Wait ' + this.timeLeft + 's'
                            : 'Resend OTP';
                    }
                }"
                x-init="
                    if (timeLeft > 0) {
                        let interval = setInterval(() => {
                            timeLeft--;
                            if (timeLeft <= 0) clearInterval(interval);
                        }, 1000);
                    }
                "
                class="w-1/2 text-black"
            >
                <flux:button
                    type="button"
                    id="loading-button"
                    variant="primary"
                    color="blue"
                    class="w-full group bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                    wire:click="sendVerification"
                    wire:loading.attr="disabled"
                    x-bind:disabled="timeLeft > 0"
                >
                    <span wire:loading.remove wire:target="sendVerification">
                        <span x-text="label"></span>
                    </span>
                    <span wire:loading wire:target="sendVerification">Sending...</span>
                </flux:button>
            </div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="w-1/2">
                @csrf
                <flux:button
                    type="submit"
                    variant="danger"
                    color="rose"
                    wire:loading.attr="disabled"
                    class="w-full group bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out cursor-pointer"
                >
                    {{ __('Log Out') }}
                </flux:button>
            </form>
        </div>
    </div>
</div>
