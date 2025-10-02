<div class="w-full">
    <div class="flex justify-center items-center py-5 text-center">
        <h1 class="text-5xl font-bold tracking-tighter text-mc_primary_color dark:text-blue-600 ">
            {{ $title }}
        </h1>
    </div>

    <form wire:submit="login" class="flex flex-col gap-5">
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
            <div wire:loading wire:target="openModalRegister, login">
                <div class="w-full flex items-center justify-center gap-2">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full"></div>
                </div>
            </div>
        </div>
    </form>

    <div
        x-data="{
            show: @entangle('showSuccessModal'),
            redirectLink: @entangle('redirectLink'),
        }"
        x-init="
            $watch('show', value => {
                if (value && redirectLink) {
                    setTimeout(() => { window.location.href = redirectLink }, 2500) // 2.5s delay
                }
            })
        "
        x-cloak
    >
        <!-- Overlay -->
        <div
            class="fixed inset-0 bg-black/50 z-[90]"
            x-show="show"
            x-transition.opacity
        ></div>

        <!-- Modal -->
        <div
            class="fixed inset-0 flex items-center justify-center z-[100]"
            x-show="show"
            x-transition
        >
            <div class="shadow-lg w-[90%] sm:w-[80%] md:w-[60%] lg:w-[40%] xl:w-[30%] max-w-2xl transform transition-all">
                <div class="bg-white dark:bg-black rounded-lg flex flex-col items-center justify-center w-full">
                    <!-- Header -->
                    <div class="bg-green-600/30 flex flex-col items-center justify-center p-6 w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor"
                            class="w-52 h-52 text-green-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>

                    <!-- Body -->
                    <div class="p-6 flex flex-col items-center space-y-3 w-full">
                        <span class="text-4xl font-bold">Success</span>
                        <span class="text-[22px] font-bold text-center text-gray-700 dark:text-gray-200">
                            You have successfully logged in!
                        </span>
                        <span class="text-[18px] font-semibold text-gray-500 dark:text-gray-400">
                            Redirecting you in a moment...
                        </span>
                        <div class="w-full flex items-center justify-center gap-2">
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
