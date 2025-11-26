<div class="h-full flex items-center justify-center p-6 bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-zinc-800 rounded-2xl p-6 space-y-6">

        <div class="text-center space-y-2">
            <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-500">
                Two-Factor Authentication
            </h1>
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                Enter the 6-digit code from your authenticator app to confirm your identity.
            </p>
        </div>

        @if (auth()->user()->two_factor_secret)
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 text-center">
                    Scan this QR code with your authenticator app:
                </h2>
                <div class="flex justify-center">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
                        {!! auth()->user()->twoFactorQrCodeSvg() !!}
                    </div>
                </div>
            </div>
        @endif

        @error('two_factor')
            <div class="mb-4 flex justify-center rounded-lg bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100 p-2">
                <span class="pb-2">
                    <flux:error name="two_factor" />
                </span>
            </div>
        @enderror

        <form wire:submit.prevent="confirm" class="space-y-4">
            <flux:field class="flex flex-col gap-2">
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.key class="text-mc_primary_color dark:text-white"/>
                        <span>Authentication Code</span>
                    </flux:label>

                    <flux:input.group>
                        <flux:input
                            wire:model="code"
                            id="code"
                            name="code"
                            type="text"
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder="123456"
                            clearable
                        />
                    </flux:input.group>
                </div>
            </flux:field>

            <div class="flex flex-col gap-2">
                <flux:button
                    variant="primary"
                    color="blue"
                    type="submit"
                    class="w-full bg-mc_primary_color dark:text-white"
                    wire:target="confirm"
                    wire:loading.remove
                >
                    <span class="flex items-center justify-center gap-2">
                        <flux:icon.check />
                        <span>Verify Code</span>
                    </span>
                </flux:button>
                <div wire:loading wire:target="confirm">
                    <div class="flex justify-center gap-2 mt-2">
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                    </div>
                </div>
            </div>
        </form>

        <div class="border-t border-gray-200 dark:border-zinc-700 my-4"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <flux:button
                type="submit"
                variant="danger"
                color="rose"
                icon="arrow-left-end-on-rectangle"
                class="w-full flex items-center justify-center gap-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 transition duration-300 ease-in-out"
            >
                Log Out
            </flux:button>
        </form>

    </div>
</div>
