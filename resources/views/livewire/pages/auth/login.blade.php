<div class="w-full">
    <div class="flex justify-center items-center py-3 text-center">
        <h1 class="text-7xl font-bold tracking-tighter text-mc_primary_color dark:text-blue-600 ">
            {{ $title }}
        </h1>
    </div>

    <form wire:submit="login" class="flex flex-col gap-5">
        <flux:error name="status" />
            <flux:field>
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.at-symbol/>
                        <span>Email</span>
                    </flux:label>
                    <flux:input.group>
                            <flux:input
                                wire:model="form.email"
                                id="email"
                                type="text"
                                placeholder="Enter your email"
                                class:input="text-lg font-semibold"
                                clearable
                            />

                    </flux:input.group>
                </div>

                     <flux:error name="form.email" />
            </flux:field>

            <flux:field>
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.key/>
                        <span>Password</span>
                    </flux:label>

                <flux:input.group>
                    <flux:input
                        wire:model="form.password"
                        id="password"
                        type="password"
                        class:input="hide-password-toggle text-lg font-semibold"
                        placeholder="Enter your password"
                        viewable
                        clearable
                    />
                </flux:input.group>
                </div>

                <flux:error name="form.password" />
            </flux:field>

        <div class="flex justify-between mt-4"  wire:target="openModalRegister, login" wire:loading.remove>
            <flux:field variant="inline">
                <flux:checkbox
                    wire:model="form.remember"
                />
                <flux:label for="remember">{{ __('Remember me') }}</flux:label>
            </flux:field>

            <div class="flex justify-end">
                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="underline-none text-sm font-bold text-blue-600 hover:text-blue-900 rounded-md transition duration-300 ease-in-out"
                        wire:target="openModalRegister, login"
                        wire:loading.class="cursor-not-allowed pointer-events-none"
                        wire:navigate
                    >
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="flex flex-col items-center gap-2 justify-end mt-4 w-full">
            <flux:button
                variant="primary"
                color="blue"
                class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                wire:click="login"
                wire:target="openModalRegister, login"
                wire:loading.attr="disabled"
                wire:loading.remove
                >
                    <span class="flex items-center justify-center gap-2">
                        <span>
                            <flux:icon.arrow-right-end-on-rectangle/>
                        </span>
                        <span>{{ __('Log in') }} </span>
                    </span>
            </flux:button>

           <div class="w-full flex items-center justify-center">
                <flux:button
                    type="button"
                    variant="primary"
                    class="w-full transition duration-300 border !border-black hover:!bg-gray-100 dark:hover:!bg-zinc-600 !bg-white dark:!bg-zinc-800 dark:!border-white dark:!text-white ease-in-out"
                    wire:click="openModalRegister"
                    wire:target="openModalRegister, login"
                    wire:loading.attr="disabled"
                    wire:loading.remove
                >
                    <span class="flex items-center justify-center gap-2">
                        <flux:icon.pencil-square />
                        <span>{{ __('Register') }}</span>
                    </span>
                </flux:button>
            </div>
            <div wire:loading wire:target="openModalRegister, login">
                <div class="w-full flex items-center justify-center gap-2">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>
    </form>

    <flux:modal wire:model.self="showSuccessModal" class="md:w-1/2" :closable="false" :dismissible="false">
        <div
            x-data="{
                redirectLink: @entangle('redirectLink'),
            }"
            x-init="
                $watch('$wire.showSuccessModal', value => {
                    if (value && redirectLink) {
                        setTimeout(() => { window.location.href = redirectLink }, 2500)
                    }
                })
            "
            class="flex flex-col items-center justify-center w-full"
        >
            <div class="relative">
                <img
                    src="{{ asset('/images/check.png') }}"
                    class="w-full h-48 sm:h-56 object-cover"
                    alt="Login Success Background"
                >
            </div>

            <div class="flex flex-col items-center space-y-3 w-full">
                <span class="text-4xl font-bold text-blue-600">Success</span>
                <span class="text-[22px] font-bold text-center text-gray-700 dark:text-gray-200">
                    You have successfully logged in!
                </span>
                <span class="text-[18px] font-semibold text-gray-500 dark:text-gray-400">
                    Redirecting you in a moment...
                </span>

                <div class="flex items-center justify-center gap-2">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>
    </flux:modal>

    <div
        x-data="{ open: false }"
        x-init="@if(session('message')) open = true; @endif"
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
                    {{ session('message') }}
                </span>
                <span class="text-sm font-semibold text-center">
                    You can now proceed.
                </span>
            </div>
            <flux:button
                icon="x-mark"
                variant="subtle"
                @click="open = false"
            >
                Close
            </flux:button>
        </div>
    </div>

</div>
