<div class="px-10 py-5 flex flex-col justify-center items-center w-full">
    <div class="p-6 bg-gray-200/20 dark:bg-zinc-800/50 rounded-lg shadow-md">

        <form wire:submit.prevent="submit" class="space-y-4">

            <!-- Grievance Form -->
            {{ $this->form }}

            <!-- Existing Attachments -->
            @if(!empty($existing_attachments))
                <div class="mt-4">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300">Existing Attachments:</h4>
                    <ul class="space-y-2">
                        @foreach($existing_attachments as $attachment)
                            <li class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-2 rounded">
                                <a href="{{ Storage::url($attachment['file_path']) }}" target="_blank" class="flex items-center gap-2">
                                    @php
                                        $extension = pathinfo($attachment['file_name'], PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp

                                    @if($isImage)
                                        <img src="{{ Storage::url($attachment['file_path']) }}"
                                            alt="{{ $attachment['file_name'] }}"
                                            class="w-16 h-16 object-cover rounded shadow hover:scale-105 transition">
                                    @else
                                        <x-heroicon-o-document class="w-8 h-8 text-gray-500" />
                                    @endif

                                    <span class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $attachment['file_name'] }}
                                    </span>
                                </a>

                                <flux:button
                                    variant="danger"
                                    icon="trash"
                                    type="button"
                                    x-on:click="$dispatch('open-modal', 'confirm-delete-{{ $attachment['attachment_id'] }}')">
                                    Remove
                                </flux:button>
                            </li>

                            <!-- Delete confirmation modal -->
                            <x-modal name="confirm-delete-{{ $attachment['attachment_id'] }}" class="p-6">
                                <div class="flex items-center space-x-3 p-4">
                                    <!-- Warning Icon -->
                                    <div class="flex-shrink-0">
                                        <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                                    </div>

                                    <!-- Title & Message -->
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            Confirm Deletion
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Are you sure you want to delete
                                            <span class="font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $attachment['file_name'] }}
                                            </span>?
                                            This action <span class="text-red-600 dark:text-red-400">cannot be undone</span>.
                                        </p>
                                    </div>
                                </div>

                                <!-- Footer Buttons -->
                                <div class="mt-6 flex justify-end space-x-3 p-4">
                                    <flux:button
                                        variant="primary"
                                        color="zinc"
                                        class="px-4 py-2 rounded-md"
                                        x-on:click="$dispatch('close-modal', 'confirm-delete-{{ $attachment['attachment_id'] }}')">
                                        Cancel
                                    </flux:button>

                                    <flux:button
                                        variant="danger"
                                        icon="trash"
                                        class="px-4 py-2 rounded-md"
                                        wire:click="removeAttachment({{ $attachment['attachment_id'] }})"
                                        x-on:click="$dispatch('close-modal', 'confirm-delete-{{ $attachment['attachment_id'] }}')">
                                        Yes, Delete
                                    </flux:button>
                                </div>
                            </x-modal>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4">
                <flux:button
                    icon="pencil-square"
                    variant="primary"
                    color="blue"
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full">
                    Update Grievance
                </flux:button>
            </div>

        </form>

    </div>
</div>

