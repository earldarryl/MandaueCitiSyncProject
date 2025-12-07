<div>
    <div class="flex justify-center items-center py-3 text-center">
        <h1 class="text-6xl font-bold tracking-tighter text-mc_primary_color dark:text-blue-600 ">
            {{ $title }}
        </h1>
    </div>

    <div class="my-4 text-sm text-justify indent-4 bg-gray-200 dark:bg-zinc-800 p-5 rounded-md">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <div
        x-data="{ open: false }"
        x-init="@if(session('status')) open = true; @endif"
        x-cloak
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center"
    >
        <div
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 bg-black/50"
        ></div>

        <div
            x-show="open"
            x-transition
            class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg max-w-md w-full p-6 flex flex-col items-center gap-4 z-50"
        >
            <div class="relative">
                <img
                    src="{{ asset('/images/check.png') }}"
                    class="w-full h-48 sm:h-56 object-cover"
                    alt="Registration Success Background"
                >
            </div>

            <div class="flex flex-col items-center space-y-2 w-full">
                <span class="text-4xl font-bold text-blue-600">Success!</span>
                <span class="text-md font-semibold text-center text-gray-700 dark:text-gray-200">
                    {{ session('status') }}
                </span>
                <span class="text-sm font-semibold text-center">
                    You can now proceed by checking your inbox.
                </span>
            </div>
            <flux:button
                icon="x-mark"
                variant="ghost"
                class="border border-gray-300 dark:border-gray-500"
                @click="open = false"
            >
                Close
            </flux:button>
        </div>
    </div>

    <form wire:submit.prevent="sendPasswordResetLink">

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
