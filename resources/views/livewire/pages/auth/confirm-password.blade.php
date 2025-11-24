<div class="flex justify-center items-center w-full min-h-screen bg-gray-100 dark:bg-zinc-900">
    <div class="w-full max-w-md p-6">
        <div class="bg-white dark:bg-zinc-800 shadow-xl rounded-2xl p-6">
            <h2 class="text-xl font-semibold text-center text-gray-800 dark:text-white">
                {{ __('Confirm Password') }}
            </h2>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 text-center">
                {{ __('Please confirm your password before continuing.') }}
            </p>

            <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-4">
                @csrf

                @error('password')
                    <div class="mb-4 flex justify-center rounded-lg bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                        <span class="pb-2">
                            <flux:error name="password" />
                        </span>
                    </div>
                @enderror

                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2 items-center">
                        <flux:icon.lock-closed class="text-mc_primary_color dark:text-white"/>
                        <span>{{ __('Password') }}</span>
                    </flux:label>

                    <flux:input.group>
                        <flux:input
                            id="password"
                            name="password"
                            type="password"
                            wire:model="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            viewable
                            clearable
                        />
                    </flux:input.group>
                </div>

                <div class="flex justify-center mt-4">
                    <flux:button
                        type="submit"
                        variant="primary"
                        color="blue"
                        class="bg-mc_primary_color dark:bg-blue-500 w-full"
                    >
                        <span class="flex gap-2 items-center justify-center">
                            <flux:icon.check />
                            <span>{{ __('Confirm Password') }}</span>
                        </span>
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
