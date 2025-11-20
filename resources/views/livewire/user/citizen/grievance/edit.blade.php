<div class="p-4 flex flex-col justify-between gap-2 bg-white dark:bg-black w-full border border-gray-300 dark:border-zinc-700 bg-gray-200/20 dark:bg-zinc-800/50"
     x-data="{ showModal: @entangle('showConfirmSubmitModal') }">

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

                <div
                    x-data="{
                        department: @entangle('department'),
                        grievanceType: @entangle('grievance_type'),
                        grievanceCategory: @entangle('grievance_category'),

                        categoriesMap: {
                            'Business Permit and Licensing Office': {
                                'Complaint': [
                                    'Delayed Business Permit Processing',
                                    'Unclear Requirements or Procedures',
                                    'Unfair Treatment by Personnel'
                                ],
                                'Inquiry': [
                                    'Business Permit Requirements Inquiry',
                                    'Renewal Process Clarification',
                                    'Schedule or Fee Inquiry'
                                ],
                                'Request': [
                                    'Document Correction or Update Request',
                                    'Business Record Verification Request',
                                    'Appointment or Processing Schedule Request'
                                ],
                            },
                            'Traffic Enforcement Agency of Mandaue': {
                                'Complaint': [
                                    'Traffic Enforcer Misconduct',
                                    'Unjust Ticketing or Penalty',
                                    'Inefficient Traffic Management'
                                ],
                                'Inquiry': [
                                    'Traffic Rules Clarification',
                                    'Citation or Violation Inquiry',
                                    'Inquiry About Traffic Assistance'
                                ],
                                'Request': [
                                    'Request for Traffic Assistance',
                                    'Request for Event Traffic Coordination',
                                    'Request for Violation Review'
                                ],
                            },
                            'City Social Welfare Services': {
                                'Complaint': [
                                    'Discrimination or Neglect in Assistance',
                                    'Delayed Social Service Response',
                                    'Unprofessional Staff Behavior'
                                ],
                                'Inquiry': [
                                    'Assistance Program Inquiry',
                                    'Eligibility or Requirements Clarification',
                                    'Social Service Schedule Inquiry'
                                ],
                                'Request': [
                                    'Request for Social Assistance',
                                    'Financial Aid or Program Enrollment Request',
                                    'Home Visit or Consultation Request'
                                ],
                            }
                        },

                        get departmentOptions() {
                            return Object.keys(this.categoriesMap);
                        },

                        get typeOptions() {
                            return this.department ? Object.keys(this.categoriesMap[this.department]) : [];
                        },

                        get categoryOptions() {
                            return this.department && this.grievanceType
                                ? this.categoriesMap[this.department][this.grievanceType]
                                : [];
                        },

                        resetType() {
                            this.grievanceType = '';
                            this.grievanceCategory = '';
                            $wire.set('grievance_type', '', true);
                            $wire.set('grievance_category', '', true);
                        },

                        resetCategory() {
                            this.grievanceCategory = '';
                            $wire.set('grievance_category', '', true);
                        }
                    }"
                    class="flex flex-col gap-6"
                >

                    <!-- Department -->
                    <flux:field>
                        <div class="flex flex-col gap-2">

                            <flux:label class="flex gap-2 items-center">
                                <flux:icon.building-office />
                                <span>Department</span>
                            </flux:label>

                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                Which department is involved or related to your grievance?
                            </h3>

                            <div
                                x-data="{
                                    open: false,
                                    search: '',
                                    selected: @entangle('department'),
                                    optionsMap: @js($departmentOptions),
                                    highlightedIndex: -1,

                                    get optionsList() { return Object.entries(this.optionsMap); },
                                    get filteredOptions() {
                                        if(!this.search) return this.optionsList;
                                        const q = this.search.toLowerCase();
                                        return this.optionsList.filter(([k,v]) => v.toLowerCase().includes(q));
                                    },
                                    selectOption(key) {
                                        this.selected = key;
                                        $wire.set('department', key, true);
                                        this.open = false;
                                        this.search = '';
                                        this.highlightedIndex = -1;
                                        $dispatch('department-selected', { value: key });
                                    },
                                    get displayValue() { return this.optionsMap[this.selected] ?? this.selected ?? ''; },
                                }"
                                x-init="
                                    $watch('displayValue', val => $wire.set('department', val, true));
                                    window.addEventListener('clear', () => { selected = ''; search=''; highlightedIndex=-1; $wire.set('department','',true); });
                                "
                                class="relative w-full"
                            >
                                <!-- Input -->
                                <div
                                    @click="open = !open; if(open) highlightedIndex=0"
                                    tabindex="0"
                                    class="relative !cursor-pointer"
                                >
                                    <flux:input
                                        name="department"
                                        readonly
                                        placeholder="Select department"
                                        class:input="border rounded-lg w-full cursor-pointer select-none !cursor-pointer"
                                        x-bind:value="displayValue"
                                    />

                                    <div class="absolute right-3 inset-y-0 flex items-center gap-2">
                                        <flux:button
                                            x-show="!!selected"
                                            size="sm"
                                            variant="subtle"
                                            icon="x-mark"
                                            class="h-5 w-5"
                                            @click.stop="selected = ''; search=''; highlightedIndex=-1; $wire.set('department','',true);"
                                        />
                                        <div class="h-5 w-5 flex items-center justify-center">
                                            <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'
                                                stroke-width='2' stroke='currentColor'
                                                class='h-5 w-5 text-gray-500 transition-transform duration-200'
                                                :class='open ? `rotate-180` : ``'>
                                                <path stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5' />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown -->
                                <div
                                    x-show="open"
                                    @click.outside="open = false; highlightedIndex=-1"
                                    x-transition
                                    class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white ring-1 ring-gray-200 dark:ring-zinc-700 rounded-md shadow-md max-h-48 overflow-y-auto"
                                >
                                    <!-- Search -->
                                    <div class="w-full flex items-center border-b border-gray-300 dark:border-zinc-700 p-1">
                                        <flux:icon.magnifying-glass class="px-1 text-gray-500 dark:text-zinc-700"/>
                                        <input
                                            type="text"
                                            x-model="search"
                                            placeholder="Search..."
                                            class="w-full border-none focus:outline-none focus:ring-0 bg-transparent placeholder-gray-400 py-1 text-sm font-medium"
                                            @keydown.arrow-down.prevent="if(highlightedIndex < filteredOptions.length-1) highlightedIndex++"
                                            @keydown.arrow-up.prevent="if(highlightedIndex > 0) highlightedIndex--"
                                            @keydown.enter.prevent="if(filteredOptions[highlightedIndex]) selectOption(filteredOptions[highlightedIndex][0])"
                                        />
                                    </div>

                                    <!-- Options -->
                                    <div class="max-h-48 overflow-y-auto">
                                        <ul class="py-1" role="listbox">
                                            <template x-for="[key,label], index in filteredOptions" :key="key">
                                                <li>
                                                    <button
                                                        type="button"
                                                        @click="selectOption(key)"
                                                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                                        :class="selected === key
                                                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                                            : index === highlightedIndex
                                                                ? 'bg-zinc-100 dark:bg-zinc-800'
                                                                : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' "
                                                    >
                                                        <span x-text="label"></span>
                                                        <flux:icon.check x-show="selected === key" class="w-4 h-4" />
                                                    </button>
                                                </li>
                                            </template>

                                            <li
                                                x-show="filteredOptions.length === 0"
                                                class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                No results found
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <flux:error name="department" />
                    </flux:field>

                    <!-- Grievance Type -->
                    <flux:field x-show="department" x-cloak>
                        <div class="flex flex-col gap-2" x-data="{ open: false, search: '' }">
                            <flux:label class="flex gap-2 items-center">
                                <flux:icon.squares-2x2 />
                                <span>Grievance Type</span>
                            </flux:label>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                What kind of grievance would you like to file?
                            </h3>

                            <div class="relative !cursor-pointer">
                                <flux:input
                                    name="grievance_type"
                                    readonly
                                    x-model="grievanceType"
                                    placeholder="Select grievance type"
                                    @click="open = !open"
                                    class:input="border rounded-lg w-full cursor-pointer select-none !cursor-pointer"
                                />

                                <div
                                    x-show="open"
                                    @click.outside="open = false; search=''"
                                    x-transition
                                    class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-900 ring-1 ring-gray-200 dark:ring-zinc-700 rounded-md shadow-md max-h-48 overflow-y-auto"
                                >
                                    <div class="w-full flex items-center border-b border-gray-300 dark:border-zinc-700 p-1">
                                        <flux:icon.magnifying-glass class="px-1 text-gray-500 dark:text-zinc-700"/>
                                        <input
                                            type="text"
                                            x-model="search"
                                            placeholder="Search..."
                                            class="w-full border-none focus:outline-none focus:ring-0 bg-transparent placeholder-gray-400 py-1 text-sm font-medium"
                                        />
                                    </div>

                                    <ul class="py-1">
                                        <template x-for="opt in typeOptions.filter(t => t.toLowerCase().includes(search.toLowerCase()))" :key="opt">
                                            <li>
                                                <button
                                                    type="button"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-zinc-800"
                                                    @click="
                                                        grievanceType = opt;
                                                        $wire.set('grievance_type', opt, true);
                                                        resetCategory();
                                                        open = false;
                                                        search = '';
                                                    "
                                                    x-text="opt"
                                                ></button>
                                            </li>
                                        </template>

                                        <li x-show="typeOptions.filter(t => t.toLowerCase().includes(search.toLowerCase())).length === 0"
                                            class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                            No results found
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <flux:error name="grievance_type" />
                    </flux:field>

                    <!-- Grievance Category -->
                    <flux:field x-show="grievanceType" x-cloak>
                        <div class="flex flex-col gap-2" x-data="{ open: false, search: '' }">
                            <flux:label class="flex gap-2 items-center">
                                <flux:icon.list-bullet />
                                <span>Grievance Category</span>
                            </flux:label>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                Choose a category based on the selected department and grievance type.
                            </h3>

                            <div class="relative !cursor-pointer">
                                <flux:input
                                    name="grievance_category"
                                    readonly
                                    x-model="grievanceCategory"
                                    placeholder="Select grievance category"
                                    @click="open = !open"
                                    class:input="border rounded-lg w-full cursor-pointer select-none !cursor-pointer"
                                />

                                <div
                                    x-show="open"
                                    @click.outside="open = false; search=''"
                                    x-transition
                                    class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-900 ring-1 ring-gray-200 dark:ring-zinc-700 rounded-md shadow-md max-h-48 overflow-y-auto"
                                >
                                    <div class="w-full flex items-center border-b border-gray-300 dark:border-zinc-700 p-1">
                                        <flux:icon.magnifying-glass class="px-1 text-gray-500 dark:text-zinc-700"/>
                                        <input
                                            type="text"
                                            x-model="search"
                                            placeholder="Search..."
                                            class="w-full border-none focus:outline-none focus:ring-0 bg-transparent placeholder-gray-400 py-1 text-sm font-medium"
                                        />
                                    </div>

                                    <ul class="py-1">
                                        <template x-for="opt in categoryOptions.filter(c => c.toLowerCase().includes(search.toLowerCase()))" :key="opt">
                                            <li>
                                                <button
                                                    type="button"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-zinc-800"
                                                    @click="
                                                        grievanceCategory = opt;
                                                        $wire.set('grievance_category', opt, true);
                                                        open = false;
                                                        search = '';
                                                    "
                                                    x-text="opt"
                                                ></button>
                                            </li>
                                        </template>

                                        <li x-show="categoryOptions.filter(c => c.toLowerCase().includes(search.toLowerCase())).length === 0"
                                            class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                            No results found
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <flux:error name="grievance_category" />
                    </flux:field>

                </div>

                <flux:field class="flex-1">
                    <div class="flex flex-col gap-2">
                        <flux:label class="flex gap-2 items-center">
                            <flux:icon.clock />
                            <span>Priority Level</span>
                        </flux:label>

                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            How urgent is this grievance?
                        </h3>

                        <x-searchable-select
                            name="priority_level"
                            wire:model="priority_level"
                            placeholder="Select priority level"
                            :options="[
                                'Low' => 'Low',
                                'Normal' => 'Normal',
                                'High' => 'High',
                            ]"
                            :selected="$priority_level"
                        />
                    </div>
                    <flux:error name="priority_level" />
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
                <flux:button
                    variant="primary"
                    @click="showModal = true"
                    icon="check"
                    color="blue"
                    type="button"
                    class="w-full bg-mc_primary_color dark:bg-blue-700 transition duration-300 ease-in-out"
                    wire:loading.attr="disabled"
                    wire:target="submit"
                >
                    <span wire:loading.remove wire:target="submit">Update</span>
                    <span wire:loading wire:target="submit">Processing..</span>
                </flux:button>
            </div>
        </div>
    </div>

    <div class="flex flex-col w-full p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm"
        x-data="{ showMore: false, zoomSrc: null }">

        @if(!empty($existing_attachments))
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-folder class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                <h4 class="text-[14px] font-semibold text-gray-700 dark:text-gray-300 tracking-wide">
                    Existing Attachments
                </h4>
            </div>

            @php
                $visibleAttachments = collect($existing_attachments)->take(4);
                $extraAttachments = collect($existing_attachments)->slice(3);
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach($visibleAttachments as $index => $attachment)
                    @php
                        $extension = pathinfo($attachment['file_name'], PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
                        $url = Storage::url($attachment['file_path']);
                    @endphp

                    @if ($loop->iteration < 4 || $extraAttachments->isEmpty())
                        <div class="group relative bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden transition-all duration-200 hover:shadow-md">
                            @if($isImage)
                                <img
                                    src="{{ $url }}"
                                    alt="{{ $attachment['file_name'] }}"
                                    class="w-full h-36 object-cover cursor-pointer transition-all duration-200 group-hover:opacity-85"
                                    @click.stop.prevent="zoomSrc = '{{ $url }}'"
                                />
                            @else
                                <a href="{{ $url }}" target="_blank"
                                class="flex flex-col items-center justify-center gap-2 py-6 px-3 text-center hover:bg-gray-200/60 dark:hover:bg-gray-700/60 transition-all duration-200">
                                    <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />
                                    <span class="text-sm font-semibold truncate w-full text-gray-800 dark:text-gray-200">
                                        {{ $attachment['file_name'] }}
                                    </span>
                                </a>
                            @endif

                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-horizontal" class="!p-2 !rounded-full bg-white/80 dark:bg-black/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition" />
                                    <flux:menu>
                                        <flux:menu.item
                                            icon="arrow-down-tray"
                                            tag="a"
                                            href="{{ $url }}"
                                            download="{{ $attachment['file_name'] }}">
                                            Download
                                        </flux:menu.item>
                                        <flux:menu.item
                                            icon="trash"
                                            variant="danger"
                                            @click="$dispatch('open-delete-attachment-modal-{{ $attachment['attachment_id'] }}')">
                                            Delete
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                        </div>
                    @elseif ($loop->iteration === 4 && !$extraAttachments->isEmpty())
                        <div class="relative bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden cursor-pointer group"
                            @click="showMore = true">
                            @if($isImage)
                                <img src="{{ $url }}" class="w-full h-36 object-cover opacity-60" />
                            @else
                                <div class="flex items-center justify-center w-full h-36 bg-gray-200 dark:bg-gray-700">
                                    <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />
                                </div>
                            @endif
                            <div class="absolute inset-0 flex items-center justify-center bg-black/60 text-white font-semibold text-lg">
                                +{{ count($existing_attachments) - 3 }} more
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div
                x-show="showMore"
                x-transition.opacity
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
                @click.self="showMore = false">
                <div
                    x-transition.scale
                    class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-5xl w-[90%] max-h-[85vh] overflow-hidden">
                    <header class="sticky top-0 bg-white dark:bg-gray-900 z-10 px-6 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-lg sm:text-xl font-semibold text-gray-700 dark:text-gray-300 tracking-wide">
                            <x-heroicon-o-folder-plus class="w-6 h-6 sm:w-7 sm:h-7 text-gray-500 dark:text-gray-400" />
                            More Attachments
                        </h2>
                        <button
                            @click="showMore = false"
                            class="text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 border border-gray-300 dark:border-zinc-700 rounded-full p-2 transition-all duration-200"
                            aria-label="Close">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </header>

                    <div class="p-6 overflow-y-auto max-h-[70vh]">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
                            @foreach ($extraAttachments as $attachment)
                                @php
                                    $url = Storage::url($attachment['file_path']);
                                    $extension = pathinfo($attachment['file_name'], PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
                                @endphp
                                <div class="group relative bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden transition-all duration-200 hover:shadow-md">
                                    @if($isImage)
                                        <img
                                            src="{{ $url }}"
                                            alt="{{ $attachment['file_name'] }}"
                                            class="w-full h-40 object-cover cursor-pointer group-hover:opacity-85 transition"
                                            @click="zoomSrc = '{{ $url }}'"
                                        />
                                    @else
                                        <a href="{{ $url }}" target="_blank"
                                            class="flex flex-col items-center justify-center gap-2 py-6 px-3 text-center hover:bg-gray-200/60 dark:hover:bg-gray-700/60 transition">
                                            <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />
                                            <span class="text-sm font-medium truncate w-full text-gray-800 dark:text-gray-200">
                                                {{ $attachment['file_name'] }}
                                            </span>
                                        </a>
                                    @endif

                                    <!-- Dropdown -->
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <flux:dropdown>
                                            <flux:button icon="ellipsis-horizontal" class="!p-2 !rounded-full bg-white/80 dark:bg-black/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition" />
                                            <flux:menu>
                                                <flux:menu.item icon="arrow-down-tray" tag="a" href="{{ $url }}" download="{{ $attachment['file_name'] }}">Download</flux:menu.item>
                                                <flux:menu.item icon="trash" variant="danger" @click="$dispatch('open-delete-attachment-modal-{{ $attachment['attachment_id'] }}')">Delete</flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div
                x-show="zoomSrc"
                x-cloak
                class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90"
                @click.self="zoomSrc = null">
                <div class="relative max-w-5xl w-[90%] flex items-center justify-center">
                    <img :src="zoomSrc" class="w-full max-h-[85vh] object-contain rounded-lg shadow-lg" />
                    <div class="absolute top-4 right-4 flex items-center gap-2">
                        <a :href="zoomSrc" download class="bg-black/60 hover:bg-black/80 text-white rounded-full p-2 transition" title="Download Image">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                        </a>
                        <button @click="zoomSrc = null" class="bg-black/60 hover:bg-black/80 text-white rounded-full p-2 transition" title="Close">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>

        @else
            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400">
                <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
                <p class="text-sm font-medium">No existing attachments</p>
            </div>
        @endif
    </div>

    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center"
    >
        <div
            class="absolute inset-0 bg-black/50"
            @click="showModal = false"
            x-transition.opacity
        ></div>

        <div
            class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg max-w-md w-full mx-4 overflow-hidden z-50"
            x-transition.scale
        >
            <div class="relative">
                <img
                    src="{{ asset('/images/confirmation-submit-bg.png') }}"
                    class="w-full h-48 sm:h-56 object-cover"
                    alt="Feedback Background"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            </div>

            <div class="flex flex-col gap-2 justify-center items-center p-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                    Confirm Update
                </h2>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300 text-center">
                    Are you sure you want to update this grievance?
                    This action will overwrite the existing details.
                </p>
            </div>

            <div class="flex items-center justify-center w-full">
                <div wire:loading.remove wire:target="submit">
                    <div class="flex justify-center gap-3 p-4 rounded-b-2xl">
                        <flux:button variant="subtle" @click="showModal = false" class="border border-gray-200 dark:border-zinc-800">Cancel</flux:button>
                        <flux:button
                            @click="showModal = false"
                            variant="primary"
                            color="blue"
                            icon="pencil-square"
                            class="bg-mc_primary_color px-4 py-2 rounded-md"
                            wire:click="submit"
                        >
                            Yes, Update
                        </flux:button>
                    </div>
                </div>

                <div wire:loading wire:target="submit">
                    <div class="flex items-center justify-center gap-2 w-full py-4">
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
