<div class="p-4 m-6 flex flex-col justify-between gap-2 bg-white dark:bg-black w-full border border-gray-300 dark:border-zinc-700 bg-gray-200/20 dark:bg-zinc-800/50">

    <x-responsive-nav-link
        href="{{ route('citizen.grievance.index') }}"
        wire:navigate
        class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
            bg-gray-100 dark:bg-zinc-800 text-gray-800 dark:text-gray-200
            border border-gray-500 dark:border-gray-200
            hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 w-full sm:w-52"
    >
        <x-heroicon-o-home class="w-5 h-5 text-gray-700 dark:text-gray-300" />
        <span class="hidden lg:inline">Return to Home</span>
        <span class="lg:hidden">Home</span>
    </x-responsive-nav-link>

    <div class="flex flex-col w-full p-3 rounded-lg">
        <div class="p-6 bg-gray-200/20 dark:bg-zinc-800/50 rounded-lg border border-gray-300 dark:border-zinc-700 w-full">

            <div class="flex flex-col gap-4 space-y-4">

                <flux:field>
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2 items-center">
                            <flux:icon.question-mark-circle />
                            <span>Is Anonymous</span>
                        </flux:label>

                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Do you want to keep your identity hidden when submitting this grievance?
                        </h3>

                        <ul class="grid w-full gap-4 md:grid-cols-1">
                            @foreach ([
                                [
                                    'id' => 'anonymous-yes',
                                    'value' => 1,
                                    'label' => 'Yes',
                                    'desc' => 'Your identity will be hidden from HR Liaisons.',
                                    'color' => 'blue',
                                    'icon' => 'eye-slash',
                                ],
                                [
                                    'id' => 'anonymous-no',
                                    'value' => 0,
                                    'label' => 'No',
                                    'desc' => 'Your name will be visible to assigned HR Liaisons.',
                                    'color' => 'amber',
                                    'icon' => 'eye',
                                ],
                            ] as $option)
                                @php $color = $option['color']; @endphp
                                <li class="relative">
                                    <label
                                        for="{{ $option['id'] }}"
                                        class="group relative inline-flex items-center justify-between w-full p-5
                                            rounded-xl cursor-pointer shadow-sm transition-all duration-300 ease-in-out
                                            text-gray-800 dark:text-gray-200 bg-white dark:bg-zinc-900
                                            border border-gray-200 dark:border-zinc-700 hover:shadow-md overflow-hidden
                                            hover:border-{{ $color }}-400 hover:bg-{{ $color }}-50/50 dark:hover:bg-{{ $color }}-900/20"
                                    >
                                        <input
                                            type="radio"
                                            id="{{ $option['id'] }}"
                                            name="is_anonymous"
                                            value="{{ $option['value'] }}"
                                            wire:model="is_anonymous"
                                            class="hidden peer"
                                        />

                                        <span
                                            @class([
                                                'absolute inset-0 rounded-xl z-0 opacity-0 transition-all duration-300 peer-checked:opacity-100',
                                                'bg-blue-100 border-2 border-blue-500 dark:bg-blue-900/40 dark:border-blue-400' => $color === 'blue',
                                                'bg-amber-100 border-2 border-amber-500 dark:bg-amber-900/40 dark:border-amber-400' => $color === 'amber',
                                            ])>
                                        </span>

                                        <div class="relative z-10 flex flex-col gap-1">
                                            <div class="text-base font-bold flex items-center gap-2">
                                                @switch($option['icon'])
                                                    @case('eye-slash')
                                                        <flux:icon.eye-slash class="w-5 h-5" />
                                                        @break

                                                    @case('eye')
                                                        <flux:icon.eye class="w-5 h-5" />
                                                        @break
                                                @endswitch

                                                <span>{{ $option['label'] }}</span>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $option['desc'] }}</div>
                                        </div>
                                    </label>

                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <flux:error name="is_anonymous" />
                </flux:field>

                <flux:field class="flex-1">
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2 items-center">
                            <flux:icon.squares-2x2 />
                            <span>Grievance Type</span>
                        </flux:label>

                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            What kind of grievance would you like to file?
                        </h3>

                        <ul class="grid w-full gap-4 md:grid-cols-1">
                            @foreach ([
                                [
                                    'id' => 'grievance-complaint',
                                    'value' => 'Complaint',
                                    'label' => 'Complaint',
                                    'desc' => 'Reports an issue or dissatisfaction needing corrective action.',
                                    'color' => 'amber',
                                    'icon' => 'hand-raised',
                                ],
                                [
                                    'id' => 'grievance-inquiry',
                                    'value' => 'Inquiry',
                                    'label' => 'Inquiry',
                                    'desc' => 'Seeks clarification or information on HR-related matters.',
                                    'color' => 'violet',
                                    'icon' => 'question-mark-circle',
                                ],
                                [
                                    'id' => 'grievance-request',
                                    'value' => 'Request',
                                    'label' => 'Request',
                                    'desc' => 'Asks for assistance, approval, or support from HR.',
                                    'color' => 'green',
                                    'icon' => 'chat-bubble-bottom-center-text',
                                ],
                            ] as $option)
                                @php $color = $option['color']; @endphp
                                <li class="relative">
                                    <label
                                        for="{{ $option['id'] }}"
                                        class="group relative inline-flex items-center justify-between w-full p-5
                                            rounded-xl cursor-pointer shadow-sm transition-all duration-300 ease-in-out
                                            text-gray-800 dark:text-gray-200 bg-white dark:bg-zinc-900
                                            border border-gray-200 dark:border-zinc-700 hover:shadow-md overflow-hidden
                                            hover:border-{{ $color }}-400 hover:bg-{{ $color }}-50/50 dark:hover:bg-{{ $color }}-900/20"
                                    >
                                        <input
                                            type="radio"
                                            id="{{ $option['id'] }}"
                                            name="grievance_type"
                                            value="{{ $option['value'] }}"
                                            wire:model="grievance_type"
                                            class="hidden peer"
                                        />

                                        <span
                                            @class([
                                                'absolute inset-0 rounded-xl z-0 opacity-0 transition-all duration-300 peer-checked:opacity-100',
                                                'bg-amber-100 border-2 border-amber-500 dark:bg-amber-900/40 dark:border-amber-400' => $color === 'amber',
                                                'bg-violet-100 border-2 border-violet-500 dark:bg-violet-900/40 dark:border-violet-400' => $color === 'violet',
                                                'bg-green-100 border-2 border-green-500 dark:bg-green-900/40 dark:border-green-400' => $color === 'green',
                                            ])>
                                        </span>

                                        <div class="relative z-10 flex flex-col gap-1">
                                            <div class="text-base font-bold flex items-center gap-2">
                                                @switch($option['icon'])
                                                    @case('hand-raised')
                                                        <flux:icon.hand-raised class="w-5 h-5" />
                                                        @break

                                                    @case('question-mark-circle')
                                                        <flux:icon.question-mark-circle class="w-5 h-5" />
                                                        @break

                                                    @case('chat-bubble-bottom-center-text')
                                                        <flux:icon.chat-bubble-bottom-center-text class="w-5 h-5" />
                                                        @break
                                                @endswitch

                                                <span>{{ $option['label'] }}</span>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $option['desc'] }}</div>
                                        </div>
                                    </label>

                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <flux:error name="grievance_type" />
                </flux:field>

                <flux:field class="flex-1">
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2 items-center">
                            <flux:icon.clock />
                            <span>Priority Level</span>
                        </flux:label>

                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            How urgent is this grievance?
                        </h3>

                        <ul class="grid w-full gap-4 md:grid-cols-1">
                            @foreach ([
                                [
                                    'id' => 'priority-low',
                                    'value' => 'Low',
                                    'label' => 'Low',
                                    'desc' => 'Can be handled later; urgent attention not required.',
                                    'color' => 'green',
                                    'icon' => 'arrow-trending-down',
                                ],
                                [
                                    'id' => 'priority-normal',
                                    'value' => 'Normal',
                                    'label' => 'Normal',
                                    'desc' => 'Standard processing applies.',
                                    'color' => 'blue',
                                    'icon' => 'arrow-long-right',
                                ],
                                [
                                    'id' => 'priority-high',
                                    'value' => 'High',
                                    'label' => 'High',
                                    'desc' => 'Requires immediate attention from HR Liaisons.',
                                    'color' => 'red',
                                    'icon' => 'arrow-trending-up',
                                ],
                            ] as $option)
                                @php $color = $option['color']; @endphp
                                <li class="relative">
                                    <label
                                        for="{{ $option['id'] }}"
                                        class="group relative inline-flex items-center justify-between w-full p-5
                                            rounded-xl cursor-pointer shadow-sm transition-all duration-300 ease-in-out
                                            text-gray-800 dark:text-gray-200 bg-white dark:bg-zinc-900
                                            border border-gray-200 dark:border-zinc-700 hover:shadow-md overflow-hidden
                                            hover:border-{{ $color }}-400 hover:bg-{{ $color }}-50/50 dark:hover:bg-{{ $color }}-900/20"
                                    >
                                        <input
                                            type="radio"
                                            id="{{ $option['id'] }}"
                                            name="priority_level"
                                            value="{{ $option['value'] }}"
                                            wire:model="priority_level"
                                            class="hidden peer"
                                        />

                                        <span
                                            @class([
                                                'absolute inset-0 rounded-xl z-0 opacity-0 transition-all duration-300 peer-checked:opacity-100',
                                                'bg-green-100 border-2 border-green-500 dark:bg-green-900/40 dark:border-green-400' => $color === 'green',
                                                'bg-blue-100 border-2 border-blue-500 dark:bg-blue-900/40 dark:border-blue-400' => $color === 'blue',
                                                'bg-red-100 border-2 border-red-500 dark:bg-red-900/40 dark:border-red-400' => $color === 'red',
                                            ])>
                                        </span>

                                        <div class="relative z-10 flex flex-col gap-1">
                                            <div class="text-base font-bold flex items-center gap-2">
                                                @switch($option['icon'])
                                                    @case('arrow-trending-down')
                                                        <flux:icon.arrow-trending-down class="w-5 h-5" />
                                                        @break

                                                    @case('arrow-long-right')
                                                        <flux:icon.arrow-long-right class="w-5 h-5" />
                                                        @break

                                                    @case('arrow-trending-up')
                                                        <flux:icon.arrow-trending-up class="w-5 h-5" />
                                                        @break
                                                @endswitch

                                                <span>{{ $option['label'] }}</span>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $option['desc'] }}</div>
                                        </div>
                                    </label>

                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <flux:error name="priority_level" />
                </flux:field>

                <flux:field>
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.building-office />
                            <span>Department</span>
                        </flux:label>

                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Which department is involved or related to your grievance?
                        </h3>

                        <x-multiple-select
                            name="department"
                            placeholder="Select department(s)"
                            :options="$departmentOptions"
                        />
                    </div>
                    <flux:error name="department" />
                </flux:field>

                <flux:field>
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2">
                            <flux:icon.tag />
                            <span>Grievance Title</span>
                        </flux:label>

                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Provide a short and descriptive title for your grievance.
                        </h3>

                        <flux:input.group>
                            <flux:input
                                wire:model="grievance_title"
                                type="text"
                                name="grievance_title"
                                placeholder="Enter your grievance title"
                                class:input="!bg-white dark:!bg-zinc-800"
                                clearable
                            />
                        </flux:input.group>
                    </div>
                    <flux:error name="grievance_title" />
                </flux:field>

                <flux:field>
                    <div class="flex flex-col gap-2 w-full">
                        <flux:label class="flex gap-2">
                            <flux:icon.document-magnifying-glass />
                            <span>Grievance Details</span>
                        </flux:label>

                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Please explain your grievance in detail for better understanding.
                        </h3>

                        {{ $this->form->getComponent('grievance_details') }}
                    </div>
                    <flux:error name="grievance_details" />
                </flux:field>

                <flux:field>
                    <div class="flex flex-col gap-2 w-full">
                        <flux:label class="flex gap-2">
                            <flux:icon.folder />
                            <span>Attachments</span>
                        </flux:label>

                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Upload any files or evidence related to your grievance.
                        </h3>

                        {{ $this->form->getComponent('grievance_files') }}
                    </div>
                    <flux:error name="grievance_files" />
                </flux:field>
            </div>

            <div class="mt-4 flex justify-end w-full">
                <flux:modal.trigger name="confirm-submit">
                    <flux:button
                        variant="primary"
                        icon="check"
                        color="blue"
                        type="button"
                        class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                    >
                        Submit
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <!-- Confirm Submit Modal -->
    <flux:modal name="confirm-submit" wire:model.self="showConfirmSubmitModal" class="md:w-96">
        <div class="flex flex-col items-center text-center p-6 space-y-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-mc_primary_color/10">
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-mc_primary_color" />
            </div>

            <flux:heading size="lg" class="font-semibold text-gray-800 dark:text-gray-100">
                Confirm Submission
            </flux:heading>

            <flux:text class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                Are you sure you want to submit this grievance? <br>
                Once submitted, it will be assigned to the HR Liaison(s).
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
                    Yes, Submit
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

    <flux:modal wire:model.self="showConfirmModal" :closable="false">
        <div class="p-6 flex flex-col items-center text-center space-y-4">
            <div class="rounded-full bg-red-100 p-3 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" />
                </svg>
            </div>

            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Missing Required Information
            </h2>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Some required fields are incomplete or invalid. Please review your input before proceeding.
            </p>

            <div class="flex justify-center gap-3 mt-4">
                <flux:button
                    variant="subtle" class="border border-gray-200 dark:border-zinc-800"
                    @click="$wire.showConfirmModal = false"
                >
                    Close
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
