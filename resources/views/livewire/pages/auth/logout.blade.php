<div
    x-data="{ show: @entangle('show') }"
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

            <div class="flex justify-end space-x-4 mt-6">
                <flux:button
                    variant="ghost"
                    @click="show = false"
                    wire:click="close"
                    wire:loading.attr="disabled"
                    wire:target="close"
                >
                    <span wire:loading.remove wire:target="close">
                        Cancel
                    </span>

                    <span wire:loading wire:target="close">
                        <span class="flex items-center gap-2">
                            <span class="animate-spin rounded-full h-4 w-4 border-2 border-gray-500 border-t-transparent"></span>
                            <span>Closing...</span>
                        </span>
                    </span>
                </flux:button>

                <flux:button
                    variant="danger"
                    wire:click="logout"
                    wire:loading.attr="disabled"
                    wire:target="logout"
                    class="flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove wire:target="logout">
                        {{ __('Log Out') }}
                    </span>

                    <span wire:loading wire:target="logout">
                        <span class="flex items-center gap-2">
                            <span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                            <span>Logging out...</span>
                        </span>
                    </span>
                </flux:button>
            </div>
        </div>
    </div>
</div>
