<div class="w-full">
    <div class="flex justify-center items-center py-3 text-center">
        <h1 class="text-7xl font-bold tracking-tighter text-mc_primary_color dark:text-blue-600 ">
            {{ $title }}
        </h1>
    </div>

    @if (session('status'))
        <div class="flex gap-3 my-4 font-medium text-sm text-green-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ session('status') }}
        </div>
    @endif
    <form wire:submit="login" class="flex flex-col gap-5">

        <!-- Email Address -->
            <flux:field>
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.at-symbol />
                        <span>Email</span>
                    </flux:label>
                    <flux:input.group>
                            <flux:input
                                wire:model="form.email"
                                id="email"
                                type="text"
                                placeholder="Enter your email"
                                clearable
                            />

                    </flux:input.group>
                </div>

                     <flux:error name="form.email" />
            </flux:field>

            <!-- Password -->
            <flux:field>
                <div class="flex flex-col gap-2">
                    <flux:label class="flex gap-2">
                        <flux:icon.key />
                        <span>Password</span>
                    </flux:label>

                <flux:input.group>
                    <flux:input
                        wire:model="form.password"
                        id="password"
                        type="password"
                        class:input="hide-password-toggle"
                        placeholder="Enter your password"
                        viewable
                        clearable
                    />
                </flux:input.group>
                </div>

                <flux:error name="form.password" />
            </flux:field>

        <!-- Remember Me -->
        <div class="flex justify-between mt-4">
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

        <div class="flex flex-col items-center gap-4 justify-end mt-4 w-full">
            <flux:button
                variant="primary"
                color="blue"
                class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                wire:click="login"
                wire:loading.attr="disabled"
                wire:target="openModalRegister, login"
                wire:loading.class="cursor-not-allowed opacity-100 pointer-events-none"
                >

                <span wire:loading wire:target="login">
                    <span class="flex items-center justify-center gap-2">
                        <span>
                            <flux:icon.loading variant="micro"/>
                        </span>
                        <span>{{ __('Logging In...') }}</span>
                    </span>
                </span>
                <span wire:loading.remove wire:target="login">
                    <span class="flex items-center justify-center gap-2">
                        <span>
                            <flux:icon.arrow-right-circle variant="micro"/>
                        </span>
                        <span>{{ __('Log in') }} </span>
                    </span>
                </span>
            </flux:button>

           <div class="w-full flex items-center justify-center">
                <flux:button
                    variant="primary"
                    color="zinc"
                    class="w-full transition duration-300 ease-in-out"
                    wire:click="openModalRegister"
                    wire:target="openModalRegister"
                    wire:loading.attr="disabled"
                    wire:loading.remove
                >
                    <span class="flex items-center justify-center gap-2">
                        <flux:icon.pencil-square variant="micro"/>
                        <span>{{ __('Register') }}</span>
                    </span>
                </flux:button>

                <div wire:loading wire:target="openModalRegister">
                    <div class="flex items-center justify-center gap-2">
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.2s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.4s]"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div
    x-data="{
        open: @entangle('isOpenModalLogin'),
        showButton: @entangle('isButtonShow'),
        redirectUrl: @entangle('redirectLink')
    }"
>
    <!-- Modal Background -->
    <div
        x-show="open"
        x-transition.opacity.duration.300ms
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        x-cloak
    >
        <!-- Modal Box -->
        <div
            x-show="open"
            x-transition.scale.duration.300ms
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg w-3/4 sm:2/4 md:2/4 lg:w-2/6 h-auto pb-8 flex flex-col gap-8"
        >
            <header class="flex justify-center items-center rounded-tr-lg rounded-tl-lg bg-green-700 text-white">
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-35">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </header>
            <div class="px-5 flex flex-col gap-8">
                <h1 class="text-3xl font-bold inline-flex justify-center items-center">
                    Login Successful!
                </h1>
                <p class="text-sm text-gray-700 dark:text-gray-400 inline-flex justify-center items-center">
                    Your account logged in successfully, you can now proceed.
                </p>
                <div class="flex justify-end">
                    <flux:button
                        x-show="showButton"
                        x-data="{ loading: false }"
                        x-on:click="
                            loading = true;
                            if (redirectUrl) {
                                Livewire.navigate(redirectUrl);
                            } else {
                                window.location.href = '{{ route('dashboard') }}';
                            }
                        "
                        variant="primary"
                        class="bg-green-700 hover:bg-green-900 text-white flex items-center justify-center tracking-widest w-full rounded-md font-bold text-xs rounded-2x1 gap-2"
                    >
                        <template x-if="!loading">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Ok</span>
                            </div>
                        </template>

                        <template x-if="loading">
                            <span class="flex items-center justify-center gap-2">
                                <span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                                <span>Loading... Please wait</span>
                            </span>
                        </template>
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
