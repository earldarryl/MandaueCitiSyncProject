<div class="w-full flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900">

    <div class="flex flex-col justify-center p-3">
        {{ $this->form }}

        <div class="mt-4 flex justify-end">
            <flux:button
                wire:click="submit"
                type="submit"
                variant="primary"
                class="w-full group bg-mc_primary_color text-white dark:bg-blue-700 hover:bg-mc_primary_color dark:hover:bg-blue-700 dark:hover:text-white transition duration-300 ease-in-out"
                wire:loading.attr="disabled"
                wire:loading.class="cursor-not-allowed"
                wire:target="submit,grievance_files">
                    <span class="flex items-center justify-center gap-2">
                        <span>
                            <flux:icon.arrow-up-tray variant="mini"/>
                        </span>
                        <span>{{ __('Submit') }}</span>
                    </span>
            </flux:button>
        </div>
    </div>

</div>
