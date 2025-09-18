<div class="w-full flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900">
    <header class="p-6 flex flex-col gap-2 bg-sky-500/10 rounded-b-lg shadow-sm">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-sky-900 dark:text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <h1 class="text-3xl font-semibold text-sky-900 dark:text-blue-500">Grievance Application Form</h1>
        </div>
    </header>

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
