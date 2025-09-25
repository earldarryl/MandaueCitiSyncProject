<div class="w-full">
    <div class="flex justify-center items-center py-3 text-center">
        <h1 class="text-7xl font-bold tracking-tighter text-mc_primary_color dark:text-blue-600 ">
            {{ $title }}
        </h1>
    </div>


    <form wire:submit="login" class="flex flex-col gap-5">
        @if (session('status'))
            <div class="flex gap-3 my-4 font-medium text-sm text-green-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                {{ session('status') }}
            </div>
        @endif
        <flux:error name="status" />
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
                            <flux:icon.arrow-right-circle variant="micro"/>
                        </span>
                        <span>{{ __('Log in') }} </span>
                    </span>
            </flux:button>

           <div class="w-full flex items-center justify-center">
                <flux:button
                    type="button"
                    variant="primary"
                    color="zinc"
                    class="w-full transition duration-300 ease-in-out"
                    wire:click="openModalRegister"
                    wire:target="openModalRegister, login"
                    wire:loading.attr="disabled"
                    wire:loading.remove
                >
                    <span class="flex items-center justify-center gap-2">
                        <flux:icon.pencil-square variant="micro"/>
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

</div>
