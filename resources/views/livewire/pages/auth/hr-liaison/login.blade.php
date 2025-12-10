<div class="w-full">
    <div class="flex justify-center items-center py-5 text-center">
        <h1 class="text-5xl font-bold tracking-tighter text-mc_primary_color dark:text-blue-600 ">
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
                            x-on:keydown.enter.prevent="$wire.call('login')"
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
                    x-on:keydown.enter.prevent="$wire.call('login')"
                />
            </flux:input.group>
            </div>

            <flux:error name="form.password" />
        </flux:field>

        <div class="flex flex-col items-center gap-2 justify-end mt-4 w-full"
            x-data="{
                    cooldown: @entangle('form.cooldown') || @entangle('form.cooldown').defer || @entangle('cooldown').defer || 0,
                    label: 'Login',
                    interval: null,
                    startCooldown(seconds) {
                        this.cooldown = seconds;
                        this.updateLabel();
                        if (this.interval) clearInterval(this.interval);
                        this.interval = setInterval(() => {
                            if (this.cooldown > 1) {
                                this.cooldown--;
                                this.updateLabel();
                            } else {
                                clearInterval(this.interval);
                                this.cooldown = 0;
                                this.updateLabel();
                            }
                        }, 1000);
                    },
                    updateLabel() {
                        this.label = this.cooldown > 0
                            ? 'Please try again in ' + this.cooldown + 's.'
                            : 'Login';
                    }
                }"
                x-init="
                    if (cooldown > 0) startCooldown(cooldown);
                    $watch('cooldown', value => {
                        if (value > 0) startCooldown(value);
                    });
                "
        >
            <flux:button
                variant="primary"
                color="blue"
                class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                wire:click="login"
                wire:target="openModalRegister, login"
                wire:loading.attr="disabled"
                wire:loading.remove
                x-bind:disabled="cooldown > 0"
                >
                    <span class="flex items-center justify-center gap-2">
                        <span>
                            <flux:icon.arrow-right-end-on-rectangle/>
                        </span>
                        <span x-text="label"></span>
                    </span>
            </flux:button>
            <div wire:loading wire:target="login">
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

</div>
