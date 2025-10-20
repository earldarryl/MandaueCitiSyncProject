<div
    x-data="{ showModal: false }"
    x-on:logout-modal.window="showModal = true"
    x-on:close-logout-modal.window="showModal = false"
    x-on:close-all-modals.window="showModal = false"
    x-cloak
>
    <div
        class="fixed inset-0 bg-black/50 z-40"
        x-show="showModal"
        x-transition.opacity
        @click="showModal = false"
    ></div>

    <div
        class="fixed inset-0 flex items-center justify-center z-[55]"
        x-show="showModal"
        x-transition.scale
    >
        <div class="bg-white dark:bg-zinc-900 flex flex-col gap-2 rounded-xl shadow-lg max-w-md w-full mx-4 p-6">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-500/20 mx-auto">
                    <x-heroicon-o-arrow-left-end-on-rectangle class="w-10 h-10 text-blue-500" />
                </div>

                <div class="flex flex-col gap-2 justify-center items-center">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                        Confirm Logout
                    </h2>

                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Are you sure you want to log out of your account?
                    </p>
                </div>

                <div wire:loading.remove wire:target="logout">
                    <div class="flex items-center justify-center w-full gap-3 mt-4">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                        >
                            Cancel
                        </button>

                        <flux:button
                            variant="danger"
                            icon="arrow-left-end-on-rectangle"
                            wire:click="logout"
                            wire:loading.attr="disabled"
                            wire:target="logout"
                        >
                            Log Out
                        </flux:button>
                    </div>
                </div>

                <div wire:loading wire:target="logout">
                    <div class="flex items-center justify-center gap-2 w-full py-2">
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

