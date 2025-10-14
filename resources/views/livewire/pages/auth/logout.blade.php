<div
    x-data="{ show: @entangle('show').live }"
    x-cloak
>
    <!-- Overlay -->
    <div
        class="fixed inset-0 bg-black/50 z-40"
        x-show="show"
        x-transition.opacity
        @click="show = false"
    ></div>

    <!-- Modal -->
    <div
        class="fixed inset-0 flex items-center justify-center z-50"
        x-show="show"
        x-transition
    >
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg max-w-md w-full mx-4 p-6">
            <flux:heading size="lg">Log Out</flux:heading>
            <flux:text class="mt-2">Are you sure you want to log out?</flux:text>

            <div class="flex flex-col w-full p-3 justify-center">
                <div wire:loading.remove wire:target="logout">
                    <div class="flex justify-end space-x-4 mt-6">
                        <flux:button
                            type="button"
                            variant="primary"
                            color="zinc"
                            icon="x-mark"
                            @click="show = false"
                        >
                            <span>Close</span>
                        </flux:button>

                        <flux:button
                            variant="danger"
                            wire:click="logout"
                            wire:loading.attr="disabled"
                            wire:target="logout"
                            class="flex items-center justify-center gap-2"
                            icon="arrow-left-end-on-rectangle"
                        >
                            <span>Log Out</span>
                        </flux:button>
                    </div>
                </div>

                <div wire:loading wire:target="logout">
                    <div class="w-full flex items-center justify-center gap-2 py-4">
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
