<div class="px-10 py-5 flex flex-col justify-center items-center w-full">
    <div class="p-6 bg-gray-200/20 dark:bg-zinc-800/50 rounded-lg shadow-md w-full">
        {{ $this->form }}

        <div class="mt-4 flex justify-end">
            <flux:button
                wire:click="submit"
                type="submit"
                variant="primary"
                class="w-full group bg-mc_primary_color text-white dark:bg-blue-700
                       hover:bg-mc_primary_color dark:hover:bg-blue-700 dark:hover:text-white
                       transition duration-300 ease-in-out"
                wire:loading.attr="disabled"
                wire:loading.class="cursor-not-allowed"
                wire:target="submit"
                x-bind:class="{ 'opacity-50 cursor-not-allowed': isUploading }"
            >
                <span>Uploading files...</span>
            </flux:button>
        </div>
    </div>
</div>
