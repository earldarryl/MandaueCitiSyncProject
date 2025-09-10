<div class="h-full flex-1 overflow-y-auto p-6">
    <div class="max-w-2xl mx-auto">
        {{-- Header --}}
        <header class="pb-6 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-4xl font-bold flex items-center gap-3 text-gray-900 dark:text-gray-100">
                <svg class="w-10 h-10 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Two-Factor Authentication
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400 text-lg">
                Activate two-factor authentication to strengthen your account’s security.
            </p>
        </header>

        {{-- Flash messages --}}
        <div class="mt-6">
            @if (session('status') === 'two-factor-authentication-enabled')
                <div class="p-4 rounded-xl bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                    ✅ Two-factor authentication has been <strong>enabled</strong>.
                </div>
            @endif

            @if (session('status') === 'two-factor-authentication-disabled')
                <div class="p-4 rounded-xl bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                    ⚠️ Two-factor authentication has been <strong>disabled</strong>.
                </div>
            @endif
        </div>

        {{-- QR Code / Forms --}}
        <div class="mt-8 bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
           @if (auth()->user()->two_factor_secret)
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Scan this QR code with your authenticator app:
                    </h2>

                    <div class="flex flex-col items-center gap-6">
                        {{-- QR Code --}}
                        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
                            {!! auth()->user()->twoFactorQrCodeSvg() !!}
                        </div>

                        {{-- OTP Display with Alpine --}}
                       <div
                            x-data="{
                                otp: @entangle('currentOtp'),
                                seconds: 30,
                                refresh() {
                                    this.seconds = 30;
                                    $wire.updateOtp(); // just triggers Livewire to update
                                }
                            }"
                            x-init="setInterval(() => {
                                seconds = seconds <= 1 ? 30 : seconds - 1;
                                if (seconds === 30) refresh();
                            }, 1000)"
                        >
                            <p class="text-4xl font-mono font-bold text-green-600" x-text="otp ?? '------'"></p>
                            <p class="text-xs text-gray-500 mt-2">Refreshing in <span x-text="seconds"></span>s</p>
                        </div>
                    </div>
                </div>

                {{-- Code confirmation form --}}
                <div class="mt-6">
                    <div class="h-full flex items-center justify-center p-6">
                        <div class="w-full max-w-md bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Two-Factor Authentication
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Enter the 6-digit code from your authenticator app to confirm your identity.
                            </p>

                            {{-- Error Message --}}
                            @error('two_factor')
                                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100 text-sm">
                                    {{ $message }}
                                </div>
                            @enderror

                            {{-- Input Form --}}
                            <form wire:submit.prevent="confirm" class="space-y-4">
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Authentication Code
                                    </label>
                                    <input type="text" id="code" wire:model="code"
                                        maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600
                                                dark:bg-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="123456" required>
                                    @error('code')
                                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-lg bg-indigo-600 hover:bg-indigo-700
                                        text-white font-medium px-4 py-2 transition">
                                    Verify Code
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <form method="POST" action="/user/two-factor-authentication" class="mt-6">
                    @csrf
                    @method('DELETE')
                    <flux:button variant="danger" type="submit" class="w-full">
                        Disable Two-Factor Authentication
                    </flux:button>
                </form>

            @else
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                    Secure your account with 2FA
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Click the button below to generate a QR code and enable two-factor authentication.
                </p>

                <form method="POST" action="/user/two-factor-authentication">
                    @csrf
                    <flux:button variant="primary" type="submit" class="w-full">
                        Enable Two-Factor Authentication
                    </flux:button>
                </form>
            @endif
        </div>
    </div>
</div>
