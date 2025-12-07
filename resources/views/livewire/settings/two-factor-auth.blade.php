<div class="h-full flex-1">
        <header class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-4xl font-bold flex items-center gap-3 text-mc_primary_color dark:text-white">
                <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span>
                    Two-Factor Authentication
                </span>
            </h1>
            <p class="mt-2 font-semibold text-md text-justify text-body">
                Activate two-factor authentication to strengthen your account’s security.
            </p>
        </header>

        <div class="m-6">
            @if (session('status') === 'two-factor-authentication-enabled')
                <div class="flex items-center gap-3 p-4 mb-4 rounded-xl bg-green-50 dark:bg-green-900 shadow-sm border border-green-200 dark:border-green-700">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <div class="text-sm text-green-800 dark:text-green-100 font-medium">
                        Two-factor authentication has been <span class="font-semibold">enabled</span>.
                    </div>
                </div>
            @endif

            @if (session('status') === 'two-factor-authentication-disabled')
                <div class="flex items-center gap-3 p-4 mb-4 rounded-xl bg-yellow-50 dark:bg-yellow-900 shadow-sm border border-yellow-200 dark:border-yellow-700">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-yellow-800 dark:text-yellow-100 font-medium">
                        Two-factor authentication has been <span class="font-semibold">disabled</span>.
                    </div>
                </div>
            @endif
        </div>

        <div class="p-6 border border-gray-200 dark:border-gray-700">
           @if (auth()->user()->two_factor_secret)
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Scan this QR code with your authenticator app:
                    </h2>

                    <div class="flex flex-col items-center gap-6">
                        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
                            {!! auth()->user()->twoFactorQrCodeSvg() !!}
                        </div>

                       <div
                            x-data="{
                                otp: @entangle('currentOtp'),
                                seconds: 30,
                                refresh() {
                                    this.seconds = 30;
                                    $wire.updateOtp();
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

                <form method="POST" action="/user/two-factor-authentication" class="mx-6">
                    @csrf
                    @method('DELETE')
                    <flux:button variant="danger" type="submit" class="w-full">
                        <span class="flex gap-2 justify-center items-center">
                            <flux:icon.shield-exclamation />
                            <span>
                                Disable Two-Factor Authentication
                            </span>
                        </span>
                    </flux:button>
                </form>

            @else
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
                    Secure your account with 2FA
                </h2>
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-6">
                    Click the button below to generate a QR code and enable two-factor authentication.
                </p>

                <form method="POST" action="/user/two-factor-authentication">
                    @csrf
                    <flux:button variant="primary" color="green" type="submit" class="w-full">
                        <span class="flex gap-2 justify-center items-center">
                            <flux:icon.shield-check />
                            <span>
                                Enable Two-Factor Authentication
                            </span>
                        </span>
                    </flux:button>
                </form>
            @endif
    </div>
</div>
