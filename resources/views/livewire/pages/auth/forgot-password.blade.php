<div>
    <div class="flex justify-center items-center py-3 text-center">
        <h1 class="text-6xl font-bold tracking-tighter text-mc_primary_color dark:text-blue-600 ">
            {{ $title }}
        </h1>
    </div>

    <div class="my-4 text-sm text-justify indent-4 bg-gray-200 dark:bg-zinc-800 p-5 rounded-md">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="flex gap-3 mb-4 font-medium text-sm text-green-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="sendPasswordResetLink">

        <flux:field class="flex flex-col gap-2">
            <div class="flex flex-col gap-2">
                <flux:label class="flex gap-2">
                    <flux:icon.at-symbol/>
                    <span>Email</span>
                </flux:label>

                <flux:input.group>
                    <flux:input wire:model="email" id="email" type="email" class:input="font-semibold text-lg" placeholder="Ex. your-email@example.com" name="email" autofocus/>
                </flux:input.group>

            </div>

            <flux:error name="email" />
        </flux:field>

        <div class="flex flex-col items-center justify-center gap-4 mt-4">

            <flux:button
                    wire:click="sendPasswordResetLink"
                    variant="primary"
                    color="blue"
                    class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                    wire:loading.attr="disabled"
                    wire:loading.class="cursor-not-allowed"
                    wire:target="sendPasswordResetLink"
                    wire:loading.remove
                    >

                    <span>
                        <span class="flex items-center justify-center gap-2">
                            <span>
                                <flux:icon.envelope variant="micro"/>
                            </span>
                            <span>{{ __('Email Password Reset Link') }} </span>
                        </span>
                    </span>

                </flux:button>

            <span class="text-sm font-bold text-dark" wire:loading.remove wire:target="sendPasswordResetLink">
                <a class="underline-none text-sm font-bold text-blue-600 hover:text-blue-900 rounded-md" tabindex="-1" href="{{ route('login') }}" wire:navigate>
                    Remembered the password now?
                </a>
            </span>

            <div wire:loading wire:target="sendPasswordResetLink">
                <div class="w-full flex items-center justify-center gap-2">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>

        </div>
    </form>
</div>
