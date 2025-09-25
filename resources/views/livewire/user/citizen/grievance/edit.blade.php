<div class="p-4 flex flex-col lg:flex-row justify-between gap-2 w-full bg-gray-200/20 dark:bg-zinc-800/50">
    <div class="flex flex-col w-full bg-white dark:bg-black p-3 rounded-lg shadow-md">

        <form wire:submit.prevent="submit">
            <div class="space-y-4">

                <flux:field>
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.tag />
                            <span>Is Anonymous</span>
                        </flux:label>

                        {{ $this->form->getComponent('is_anonymous') }}

                    </div>

                    <flux:error name="grievance_title" />
                </flux:field>



                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <flux:field>
                        <div class="flex flex-col gap-2">
                            <flux:label class="flex gap-2">
                                <span>Grievance Type</span>
                            </flux:label>
                            <flux:input.group>

                            <x-searchable-select
                                name="grievance_type"
                                placeholder="Select a grievance type"
                                :options="['Complaints', 'Inquiry', 'Request', 'Suggestion/Feedback']"
                                :selected="$grievance_type"
                                />

                            </flux:input.group>
                        </div>

                        <flux:error name="grievance_type" />
                    </flux:field>

                     <flux:field>
                        <div class="flex flex-col gap-2">
                            <flux:label class="flex gap-2">
                                <span>Priority Level</span>
                            </flux:label>
                            <flux:input.group>

                            <x-searchable-select
                                name="priority_level"
                                placeholder="Select a priority level"
                                :options="['Low', 'Normal', 'High']"
                                :selected="$priority_level"
                                />

                            </flux:input.group>
                        </div>

                        <flux:error name="priority_level" />
                    </flux:field>

                </div>

                <flux:field>
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.tag />
                            <span>Department</span>
                        </flux:label>

                        {{ $this->form->getComponent('department') }}

                    </div>

                    <flux:error name="grievance_title" />
                </flux:field>

                <flux:field>
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.tag />
                            <span>Grievance Title</span>
                        </flux:label>

                        <flux:description>The first part of your full name</flux:description>

                        <flux:input.group>
                            <flux:input
                                wire:model="grievance_title"
                                type="text"
                                name="grievance_title"
                                autocomplete="grievance_title"
                                placeholder="Enter your grievance title"
                                clearable />
                        </flux:input.group>
                    </div>

                    <flux:error name="grievance_title" />
                </flux:field>

                <flux:field>
                    <div class="flex flex-col gap-2 w-full">
                        <flux:label class="flex gap-2">
                            <flux:icon.tag />
                            <span>Grievance Details</span>
                        </flux:label>

                        {{ $this->form->getComponent('grievance_details') }}

                    </div>

                    <flux:error name="grievance_title" />
                </flux:field>

                <flux:field>
                    <div class="flex flex-col gap-2 w-full">
                        <flux:label class="flex gap-2">
                            <flux:icon.tag />
                            <span>Grievance Files</span>
                        </flux:label>

                        {{ $this->form->getComponent('grievance_files') }}

                    </div>

                    <flux:error name="grievance_title" />
                </flux:field>

            </div>

            <div class="mt-4">
                <flux:button
                    icon="pencil-square"
                    variant="primary"
                    color="blue"
                    class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                    type="button"
                    x-on:click="$dispatch('open-modal', 'confirm-update')"
                >
                    Update Grievance
                </flux:button>
            </div>
        </form>
    </div>

    <x-modal name="confirm-update" class="p-6">
        <div class="flex items-center space-x-3 p-4">
            <div class="flex-shrink-0">
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-yellow-500" />
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Confirm Update
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to update this grievance?
                    This action will overwrite the existing details.
                </p>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3 p-4">
            <flux:button
                variant="primary"
                color="zinc"
                class="px-4 py-2 rounded-md"
                x-on:click="$dispatch('close-modal', 'confirm-update')"
            >
                Cancel
            </flux:button>

            <flux:button
                variant="primary"
                color="blue"
                icon="pencil-square"
                class="px-4 py-2 rounded-md"
                wire:click="submit"
                x-on:click="$dispatch('close-modal', 'confirm-update')"
            >
                Yes, Update
            </flux:button>
        </div>
    </x-modal>

    <div class="flex flex-col p-3 bg-white dark:bg-black w-full h-1/2 rounded-lg shadow-md">
        <!-- Existing Attachments -->
        @if(!empty($existing_attachments))
            <div class="mt-4">
                <h4 class="font-semibold text-gray-700 dark:text-gray-300">Existing Attachments:</h4>
                <ul class="space-y-2">
                    @foreach($existing_attachments as $attachment)
                        <li class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-2 rounded">
                            <a href="{{ Storage::url($attachment['file_path']) }}" target="_blank"
                               class="flex items-center gap-2 flex-1 min-w-0">
                                @php
                                    $extension = pathinfo($attachment['file_name'], PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp

                                @if($isImage)
                                    <img src="{{ Storage::url($attachment['file_path']) }}"
                                         alt="{{ $attachment['file_name'] }}"
                                         class="w-16 h-16 object-cover rounded shadow hover:scale-105 transition flex-shrink-0">
                                @else
                                    <x-heroicon-o-document class="w-8 h-8 text-gray-500 flex-shrink-0" />
                                @endif

                                <span class="text-blue-600 dark:text-blue-400 hover:underline truncate">
                                    {{ $attachment['file_name'] }}
                                </span>
                            </a>

                            <flux:button
                                variant="danger"
                                icon="trash"
                                type="button"
                                class="flex-shrink-0 ml-2"
                                x-on:click="$dispatch('open-modal', 'confirm-delete-{{ $attachment['attachment_id'] }}')">
                                Remove
                            </flux:button>
                        </li>

                        <!-- Delete confirmation modal -->
                        <x-modal name="confirm-delete-{{ $attachment['attachment_id'] }}" class="p-6">
                            <div class="flex items-center space-x-3 p-4">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                                </div>
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
    </div>
</div>
