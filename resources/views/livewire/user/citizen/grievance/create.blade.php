<div class="px-10 py-5 flex flex-col justify-center items-center w-full">
    <div class="p-6 bg-gray-200/20 dark:bg-zinc-800/50 rounded-lg shadow-md w-full">
        {{-- Render the Filament form --}}
        {{ $this->form }}

        <div class="mt-4 flex justify-end w-full">
        <flux:button
            variant="primary"
            icon="check"
            color="blue"
            type="button"
            class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
            x-on:click="$dispatch('open-modal', 'confirm-submit')"
            >
                Submit
            </flux:button>
        </div>
    </div>

    <x-modal name="confirm-submit" class="p-6">
    <div class="flex items-center space-x-3 p-4">
        <div class="flex-shrink-0">
            <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-blue-500" />
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Confirm Submission
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Are you sure you want to submit this grievance? <br>
                Once submitted, it will be assigned to the HR Liaison(s).
            </p>
        </div>
    </div>
    <div class="mt-6 flex justify-end space-x-3 p-4">
        <flux:button
            variant="primary"
            color="zinc"
            class="px-4 py-2 rounded-md"
            x-on:click="$dispatch('close-modal', 'confirm-submit')"
        >
            Cancel
        </flux:button>
        <flux:button
            variant="primary"
            color="blue"
            icon="pencil-square"
            class="px-4 py-2 rounded-md"
            wire:click="submit"
            x-on:click="$dispatch('close-modal', 'confirm-submit')"
        >
            Yes, Submit
        </flux:button>
    </div>
</x-modal>
</div>
