<div class="p-4 flex flex-col lg:flex-row justify-between gap-4 w-full bg-gray-200/20 dark:bg-zinc-800/50">

    <!-- LEFT COLUMN (2/3 width) -->
    <div class="flex flex-col w-full lg:w-2/3 bg-white dark:bg-black p-3 rounded-lg shadow-md">
        <div class="p-6 bg-gray-200/20 dark:bg-zinc-800/50 rounded-lg shadow-md w-full">
            <div class="flex flex-col gap-4 space-y-4">

                <!-- Is Anonymous -->
                <flux:field>
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.question-mark-circle />
                            <span>Is Anonymous</span>
                        </flux:label>

                        <flux:radio.group wire:model="is_anonymous" variant="segmented">
                            <flux:radio :value="1" label="Yes" icon="eye" description="Your identity will be hidden from HR Liaisons." />
                            <flux:radio :value="0" label="No" icon="eye-slash" description="Your name will be visible to assigned HR Liaisons." />
                        </flux:radio.group>
                    </div>
                    <flux:error name="is_anonymous" />
                </flux:field>

                <!-- Grievance Type & Priority -->
                <div class="flex flex-col lg:flex-row gap-4">
                    <!-- Grievance Type -->
                    <flux:field class="flex-1">
                        <div class="flex flex-col gap-4">
                            <flux:label class="flex gap-2">
                                <flux:icon.squares-2x2 />
                                <span>Grievance Type</span>
                            </flux:label>
                            <flux:radio.group wire:model="grievance_type">
                                <div class="bg-amber-500/20 p-3 rounded-t-lg">
                                    <flux:radio
                                        value="Complaint"
                                        label="Complaint"
                                        description="Reports an issue or dissatisfaction needing corrective action."
                                    />
                                </div>
                                <div class="bg-violet-500/20 p-3">
                                    <flux:radio
                                        value="Inquiry"
                                        label="Inquiry"
                                        description="Seeks clarification or information on HR-related matters."
                                    />
                                </div>
                                <div class="bg-green-500/20 p-3 rounded-b-lg">
                                    <flux:radio
                                        value="Request"
                                        label="Request"
                                        description="Asks for assistance, approval, or support from HR."
                                    />
                                </div>
                            </flux:radio.group>
                        </div>
                        <flux:error name="grievance_type" />
                    </flux:field>

                    <!-- Priority Level -->
                    <flux:field class="flex-1">
                        <div class="flex flex-col gap-4">
                            <flux:label class="flex gap-2">
                                <flux:icon.clock />
                                <span>Priority Level</span>
                            </flux:label>
                            <flux:radio.group wire:model="priority_level">
                                <div class="bg-green-500/20 p-3 rounded-t-lg">
                                    <flux:radio
                                        value="Low"
                                        label="Low"
                                        description="Low priority grievance. May be handled later; urgent attention is not required."
                                    />
                                </div>
                                <div class="bg-mc_primary_color/20 p-3">
                                    <flux:radio
                                        value="Normal"
                                        label="Normal"
                                        description="Normal priority grievance. Standard processing applies."
                                    />
                                </div>
                                <div class="bg-red-500/20 p-3 rounded-b-lg">
                                    <flux:radio
                                        value="High"
                                        label="High"
                                        description="High priority grievance. Requires immediate attention from HR Liaisons."
                                    />
                                </div>
                            </flux:radio.group>
                        </div>
                        <flux:error name="priority_level" />
                    </flux:field>
                </div>

                <!-- Department -->
                <flux:field>
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.building-office />
                            <span>Department</span>
                        </flux:label>
                        <x-multiple-select
                            name="department"
                            placeholder="Select department(s)"
                            :options="$departmentOptions"
                        />
                    </div>
                    <flux:error name="department" />
                </flux:field>

                <!-- Grievance Title -->
                <flux:field>
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.tag />
                            <span>Grievance Title</span>
                        </flux:label>
                        <flux:description>The title should summarize your grievance briefly.</flux:description>
                        <flux:input.group>
                            <flux:input
                                wire:model="grievance_title"
                                type="text"
                                name="grievance_title"
                                placeholder="Enter your grievance title"
                                clearable
                            />
                        </flux:input.group>
                    </div>
                    <flux:error name="grievance_title" />
                </flux:field>

                <!-- Grievance Details -->
                <flux:field>
                    <div class="flex flex-col gap-2 w-full">
                        <flux:label class="flex gap-2">
                            <flux:icon.document-magnifying-glass />
                            <span>Grievance Details</span>
                        </flux:label>
                        {{ $this->form->getComponent('grievance_details') }}
                    </div>
                    <flux:error name="grievance_details" />
                </flux:field>

                <!-- Grievance Files -->
                <flux:field>
                    <div class="flex flex-col gap-2 w-full">
                        <flux:label class="flex gap-2">
                            <flux:icon.folder />
                            <span>Grievance Files</span>
                        </flux:label>
                        {{ $this->form->getComponent('grievance_files') }}
                    </div>
                    <flux:error name="grievance_files" />
                </flux:field>
            </div>

            <!-- Update Button -->
            <div class="mt-4 flex justify-end w-full">
                <flux:modal.trigger name="confirm-update">
                    <flux:button
                        variant="primary"
                        icon="check"
                        color="blue"
                        type="button"
                        class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                    >
                        Update
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN (1/3 width, auto height) -->
    <div class="flex flex-col w-full lg:w-1/3 p-3 bg-white dark:bg-black rounded-lg shadow-md self-start">
        @if(!empty($existing_attachments))
            <div class="px-3 bg-gray-200/20 dark:bg-zinc-800/50 rounded-lg shadow-md w-full">
                <header class="relative flex gap-3 justify-start items-center py-4">
                    <flux:icon.folder class="h-6 w-6"/>
                    <h4 class="text-lg font-bold">Existing Attachments:</h4>
                </header>
                <ul class="space-y-2">
                    @foreach($existing_attachments as $attachment)
                    <li class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-2 rounded">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            @php
                                $extension = pathinfo($attachment['file_name'], PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp

                            @if($isImage)
                                <div x-data="{ show: false }" @keydown.window.escape="show = false" class="relative">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-md overflow-hidden">
                                        <img
                                            src="{{ Storage::url($attachment['file_path']) }}"
                                            alt="{{ $attachment['file_name'] }}"
                                            @click.stop.prevent="show = true"
                                            class="w-full h-full object-cover object-center cursor-pointer hover:scale-105 transition"
                                        />
                                    </div>

                                    <div
                                        x-show="show"
                                        x-transition.opacity
                                        x-cloak
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
                                        @click.self="show = false"
                                    >
                                        <div
                                            x-transition.scale
                                            class="relative max-w-[90vw] max-h-[80vh]"
                                        >
                                            <button
                                                @click="show = false"
                                                class="absolute top-2 right-2 text-white bg-black/50 rounded-full p-1 hover:bg-black transition"
                                            >
                                                <x-heroicon-o-x-mark class="w-5 h-5" />
                                            </button>

                                            <img
                                                src="{{ Storage::url($attachment['file_path']) }}"
                                                alt="{{ $attachment['file_name'] }}"
                                                class="rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 max-w-full max-h-[80vh]"
                                            />
                                        </div>
                                    </div>
                                </div>
                            @else
                                <x-heroicon-o-document class="w-8 h-8 text-gray-500 flex-shrink-0" />
                            @endif

                            <span class="text-md font-bold truncate">{{ $attachment['file_name'] }}</span>
                        </div>

                       <flux:dropdown>
                            <flux:button icon="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item
                                    icon="arrow-down-tray"
                                    tag="a"
                                    href="{{ Storage::url($attachment['file_path']) }}"
                                    download="{{ $attachment['file_name'] }}"
                                >
                                    Download
                                </flux:menu.item>
                                <flux:modal.trigger name="confirm-delete-{{ $attachment['attachment_id'] }}">
                                    <flux:menu.item
                                        icon="trash"
                                        variant="danger"
                                    >
                                        Delete
                                    </flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>
                    </li>

                    <flux:modal name="confirm-delete-{{ $attachment['attachment_id'] }}" class="md:w-1/3">
                            <div class="flex flex-col items-center text-center p-6 space-y-4">
                                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20">
                                    <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                                </div>
                                <flux:heading size="lg" class="font-bold text-gray-800 dark:text-gray-100">Confirm Deletion</flux:heading>
                                <flux:text class="text-sm leading-relaxed">
                                    Are you sure you want to delete
                                    <span class="font-bold text-gray-800 dark:text-gray-200">
                                        {{ $attachment['file_name'] }}
                                    </span>?
                                    <br>
                                    This action
                                    <span class="text-red-600 dark:text-red-400 font-bold">
                                        cannot be undone
                                    </span>.
                                </flux:text>
                            </div>
                            <div class="flex items-center justify-center w-full">
                                <div
                                    wire:loading.remove
                                    wire:target="removeAttachment({{ $attachment['attachment_id'] }})"
                                    class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 rounded-b-2xl">
                                    <flux:modal.close>
                                        <flux:button variant="subtle" class="border border-gray-200 dark:border-zinc-800">Cancel</flux:button>
                                    </flux:modal.close>
                                    <flux:button
                                        variant="danger"
                                        icon="trash"
                                        wire:click="removeAttachment({{ $attachment['attachment_id'] }})"
                                    >
                                        Yes, Delete
                                    </flux:button>
                                </div>
                                <div wire:loading wire:target="removeAttachment({{ $attachment['attachment_id'] }})">
                                    <div class="flex items-center justify-center gap-2 w-full">
                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                    </div>
                                </div>
                            </div>
                    </flux:modal>
                @endforeach
                </ul>
            </div>
        @else
        <div class="px-3 bg-gray-200/20 dark:bg-zinc-800/50 rounded-lg shadow-md w-full">
            <span class="relative flex flex-col gap-3 justify-center items-center py-4">
                <span class="bg-red-500/20 text-red-500 p-3 rounded-full">
                    <flux:icon.x-mark class="w-8 h-8"/>
                </span>
                <span class="font-bold text-lg">No Existing Attachments</span>
            </span>
        </div>
    @endif
    </div>

    <!-- Confirm Update Modal -->
    <flux:modal name="confirm-update" wire:model.self="showConfirmUpdateModal" class="md:w-96">
        <div class="flex flex-col items-center text-center p-6 space-y-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-mc_primary_color/10">
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-mc_primary_color" />
            </div>
            <flux:heading size="lg" class="font-semibold text-gray-800 dark:text-gray-100">Confirm Update</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                Are you sure you want to update this grievance?
                This action will overwrite the existing details.
            </flux:text>
        </div>

        <div class="flex items-center justify-center w-full">
            <div
                wire:loading.remove
                wire:target="submit"
                class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 rounded-b-2xl">
                <flux:modal.close>
                    <flux:button variant="subtle" class="border border-gray-200 dark:border-zinc-800">Cancel</flux:button>
                </flux:modal.close>
                <flux:button
                    variant="primary"
                    color="blue"
                    icon="pencil-square"
                    class="bg-mc_primary_color px-4 py-2 rounded-md"
                    wire:click="submit"
                >
                    Yes, Update
                </flux:button>
            </div>
            <div wire:loading wire:target="submit">
                <div class="flex items-center justify-center gap-2 w-full">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
