<div
    x-data="{ open: false, formIsValid: false }"
    @open-register-modal.window="open = true;"
    @keydown.escape.window="open = false"
    @registration-success.window="open = false; Livewire.dispatch('reset-register-form'); Livewire.dispatch('resetDropdown');"
    x-cloak
    class="relative"
>

        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50"
            style="z-index: 90"
        ></div>

        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            style="z-index: 100;"
            class="fixed inset-0 my-auto mx-auto bg-white dark:bg-zinc-900 rounded-lg w-full lg:w-2/4 h-full overflow-hidden flex flex-col"
        >
            <div class="flex flex-col flex-1 h-full"
                    x-data="{
                        currentPage: 1,
                        loading: false,

                        first_name: '',
                        middle_name: '',
                        last_name: '',
                        suffix: '',
                        gender: '',
                        civil_status: '',
                        barangay: '',
                        sitio: '',
                        birthdate: '',
                        name: '',
                        email: '',
                        contact: '',
                        password: '',
                        password_confirmation: '',
                    }"
                    x-on:step-one-validated.window="
                        stepOneValid = $event.detail.success;
                        if (stepOneValid) {
                            currentPage = 2;
                            document.getElementById('modal-scroll')?.scrollTo({top:0, behavior:'smooth'});
                        }
                    "
                    x-on:step-two-validated.window="
                        stepTwoValid = $event.detail.success;
                        if (stepTwoValid) {
                            currentPage = 3;
                            document.getElementById('modal-scroll')?.scrollTo({top:0, behavior:'smooth'});
                        }
                    "
                    x-on:register-finished.window="loading = true;"
                >
                    <div id="modal-scroll" class="overflow-y-auto flex-1">
                        <div class="sticky top-0 p-4 bg-white dark:bg-zinc-900 flex flex-col gap-3 w-full z-50">
                            <div class="flex items-center justify-between">
                                <h1 class="text-3xl tracking-tighter text-mc_primary_color dark:text-blue-600 font-extrabold">
                                    Create an account
                                </h1>
                                <flux:modal.trigger name="confirm-exit">
                                    <flux:button
                                        variant="subtle"
                                        icon="x-mark"
                                        class="h-10 w-10 rounded-full text-black dark:text-white"
                                    />
                                </flux:modal.trigger>
                            </div>
                            <div class="relative w-full flex items-center justify-center select-none">
                                <!-- Background Line (absolute behind circles) -->
                                <div class="absolute top-1/2 left-0 right-0 top-4 h-[2px] bg-gray-300 dark:bg-zinc-700 -translate-y-1/2 z-0">
                                    <!-- Active part of line -->
                                    <div
                                        class="bg-blue-500 h-[2px] absolute left-0 transition-all duration-500"
                                        :style="{ width: currentPage === 1 ? '0%' : currentPage === 2 ? '50%' : '100%' }"></div>
                                </div>

                                <div class="relative w-full flex justify-between">
                                    <!-- Step 1 -->
                                    <div class="relative z-10 flex flex-col items-start">
                                        <div :class="currentPage >= 1 ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600 dark:bg-zinc-700 dark:text-white'"
                                            class="w-8 h-8 flex items-center justify-center rounded-full font-bold transition-colors">
                                            <span x-show="currentPage == 1">1</span>
                                            <span x-show="currentPage > 1">
                                                <flux:icon.check />
                                            </span>
                                        </div>
                                        <span class="text-sm mt-1">Personal Info</span>
                                    </div>

                                    <!-- Step 2 -->
                                    <div class="relative z-10 flex flex-col items-center">
                                        <div :class="currentPage >= 2 ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600 dark:bg-zinc-700 dark:text-white'"
                                            class="w-8 h-8 flex items-center justify-center rounded-full font-bold transition-colors">
                                            <span x-show="currentPage == 1 || currentPage == 2">2</span>
                                            <span x-show="currentPage > 2">
                                                <flux:icon.check />
                                            </span>
                                        </div>
                                        <span class="text-sm mt-1">User's Account Info</span>
                                    </div>

                                    <!-- Step 3 -->
                                    <div class="relative z-10 flex flex-col items-end">
                                        <div :class="currentPage >= 3 ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600 dark:bg-zinc-700 dark:text-white'"
                                            class="w-8 h-8 flex items-center justify-center rounded-full font-bold transition-colors">

                                            <span x-show="(currentPage == 1 || currentPage == 2 || currentPage == 3) && !loading">3</span>
                                            <span x-show="loading">
                                                <flux:icon.check />
                                            </span>
                                        </div>
                                        <span class="text-sm mt-1">Summary Section</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="relative p-4">
                                <div class="relative flex flex-col w-full h-full">
                                    <div class="relative flex flex-col w-full h-full">
                                    <!-- Page 1 -->
                                    <div class="relative border-box w-full flex flex-col gap-4 px-5 pb-8 gap-4" x-show="currentPage === 1">
                                            <!-- First Name -->
                                            <flux:field>
                                                <flux:label>First Name</flux:label>

                                                <flux:description>The first part of your full name</flux:description>

                                                <flux:input.group>
                                                    <flux:input.group.prefix>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path fill="currentColor" d="M16 10v12zm1-1h-5v2h3v10h-3v2h8v-2h-3z"/></svg>
                                                    </flux:input.group.prefix>
                                                    <flux:input wire:model="first_name" x-model="first_name" id="first_name" type="text" name="first_name"
                                                            autocomplete="first_name" placeholder="Enter your first name" clearable />
                                                </flux:input.group>

                                                <flux:error name="first_name" />
                                            </flux:field>

                            <!-- Middle Name -->
                            <flux:field>
                                <flux:label>Middle Name</flux:label>

                                <flux:description>The middle part of your full name</flux:description>

                                <flux:input.group>
                                    <flux:input.group.prefix>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M7 4h8a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H9v5h8v2H7v-7a2 2 0 0 1 2-2h6V6H7z"/></svg>
                                    </flux:input.group.prefix>
                                    <flux:input wire:model="middle_name" x-model="middle_name" id="middle_name" type="text" name="middle_name"
                                            autocomplete="middle_name" placeholder="Enter your middle name" clearable />
                                </flux:input.group>

                                <flux:error name="middle_name" />
                            </flux:field>

                            <!-- Last Name -->
                            <flux:field>
                                <flux:label>Last Name</flux:label>

                                <flux:description>The last part of your full name</flux:description>

                                <flux:input.group>
                                    <flux:input.group.prefix>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M7 4h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7v-2h8v-5H8v-2h7V6H7z"/></svg>
                                    </flux:input.group.prefix>
                                    <flux:input wire:model="last_name" x-model="last_name" id="last_name" type="text" name="last_name"
                                            autocomplete="last_name" placeholder="Enter your last name" clearable />
                                </flux:input.group>

                                <flux:error name="last_name" />
                            </flux:field>

                            <!-- Dropdown with "Name Suffixes" -->
                            <flux:field>
                                <flux:label>Suffix</flux:label>
                                <flux:description>Only if you have the same name within a family (Optional)</flux:description>

                                <flux:input.group>
                                    <flux:input.group.prefix>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M8.5 4v8h7V4h2v16h-2v-6h-7a2 2 0 0 1-2-2V4z" />
                                        </svg>
                                    </flux:input.group.prefix>

                                    <div
                                        x-data="{
                                            open: false,
                                            selected: @entangle('suffix').defer,
                                            customValue: '',
                                            get value() {
                                                return this.selected === 'Other' ? this.customValue : this.selected;
                                            }
                                        }"
                                        x-init="
                                            $watch('value', (val) => {
                                                suffix = val;
                                                $wire.set('suffix', val, true);
                                            });
                                            Livewire.on('resetDropdown', () => {
                                                selected = '';
                                                customValue = '';
                                                suffix = '';
                                                open = false;
                                                $wire.set('suffix', '', true);
                                            });
                                        "
                                        class="relative w-full"
                                    >

                                        <!-- Trigger -->
                                        <div
                                            @click="open = !open"
                                            @keydown.enter.prevent="open = !open"
                                            @keydown.space.prevent="open = !open"
                                            tabindex="0"
                                            role="button"
                                            :aria-expanded="open"
                                            aria-haspopup="listbox"
                                        >
                                            <flux:input
                                                wire:model="suffix"
                                                x-model="suffix"
                                                name="suffix"
                                                class:input="border rounded-tl-none rounded-bl-none rounded-tr-lg rounded-br-lg cursor-pointer"
                                                readonly
                                                placeholder="Select a suffix"
                                                x-bind:value="selected === ''
                                                    ? ''
                                                    : (selected === 'Other'
                                                        ? (customValue || 'Custom suffix')
                                                        : selected)"
                                            />

                                        <!-- Right-side controls -->
                                        <div class="absolute right-3 inset-y-0 flex items-center gap-2">
                                            <!-- Clear button -->
                                            <flux:button
                                                x-show="selected !== ''"
                                                size="sm"
                                                variant="subtle"
                                                icon="x-mark"
                                                class="h-5 w-5"
                                                @click.stop="
                                                    selected = '';
                                                    customValue = '';
                                                    $wire.set('suffix', '', true);
                                                "
                                            />

                                            <!-- Chevron -->
                                            <div class="h-5 w-5 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor"
                                                    class="h-5 w-5 text-gray-500 transition-transform duration-200"
                                                    :class="open ? 'rotate-180' : ''">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dropdown -->
                                    <div
                                        x-show="open"
                                        @click.outside="open = false"
                                        x-transition
                                        class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white ring-1 ring-black ring-opacity-5 rounded-md shadow-md"
                                    >

                                        <ul class="py-1" role="listbox">
                                            <li>
                                                <button
                                                    @click="selected = 'N/A'; open = false"
                                                    class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                                    :class="selected === 'N/A'
                                                        ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                                        : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                                >
                                                    N/A
                                                    <flux:icon.check
                                                        x-show="selected === 'N/A'"
                                                        class="w-4 h-4" />
                                                </button>
                                            </li>

                                            <li>
                                                <button
                                                    @click="selected = 'Jr.'; open = false"
                                                    class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                                    :class="selected === 'Jr.'
                                                        ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                                        : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                                >
                                                    Jr.
                                                    <flux:icon.check
                                                        x-show="selected === 'Jr.'"
                                                        class="w-4 h-4" />
                                                </button>
                                            </li>

                                            <li>
                                                <button
                                                    @click="selected = 'Sr.'; open = false"
                                                    class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                                    :class="selected === 'Sr.'
                                                        ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                                        : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                                >
                                                    Sr.
                                                    <flux:icon.check
                                                        x-show="selected === 'Sr.'"
                                                        class="w-4 h-4" />
                                                </button>
                                            </li>

                                            <li>
                                                <button
                                                    @click="selected = 'Other'; open = true"
                                                    class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                                    :class="selected === 'Other'
                                                        ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                                        : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                                >
                                                    Other
                                                    <flux:icon.check
                                                        x-show="selected === 'Other'"
                                                        class="w-4 h-4" />
                                                </button>
                                            </li>
                                        </ul>
                                        <!-- Custom input -->
                                        <template x-if="selected === 'Other'">
                                            <div class="px-4 pb-3">
                                                <flux:input
                                                    type="text"
                                                    x-model="customValue"
                                                    placeholder="Please specify"
                                                    @click.stop
                                                    clearable
                                                    @input="$wire.set('suffix', customValue, true)"
                                                />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                        </flux:input.group>
                        <flux:error name="suffix" />
                        </flux:field>

                        <!-- Dropdown with "Gender" -->
                        <flux:field>
                            <flux:label>Gender</flux:label>
                            <flux:description>The individual's gender identity</flux:description>
                            <flux:input.group>
                                <flux:input.group.prefix>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="currentColor" class="bi bi-gender-ambiguous" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M11.5 1a.5.5 0 0 1 0-1h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1
                                                0V1.707l-3.45 3.45A4 4 0 0 1 8.5 10.97V13H10a.5.5 0 0
                                                1 0 1H8.5v1.5a.5.5 0 0 1-1 0V14H6a.5.5 0 0
                                                1 0-1h1.5v-2.03a4 4 0 1 1 3.471-6.648L14.293 1zm-.997
                                                4.346a3 3 0 1 0-5.006 3.309 3 3 0 0 0 5.006-3.31z"/>
                                    </svg>
                                </flux:input.group.prefix>

                                <div
                                    x-data="{
                                        open: false,
                                        selected: @entangle('gender').defer,
                                        customValue: '',
                                        get value() {
                                            return this.selected === 'Other' ? this.customValue : this.selected;
                                        }
                                    }"
                                    x-init="
                                        $watch('value', (val) => {
                                            gender = val;
                                            $wire.set('gender', val, true);
                                        });
                                        Livewire.on('resetDropdown', () => {
                                            selected = '';
                                            customValue = '';
                                            gender = '';
                                            open = false;
                                            $wire.set('gender', '', true);
                                        });
                                    "
                                    class="relative w-full"
                                >

                                    <!-- Trigger -->
                                    <div
                                        @click="open = !open"
                                        @keydown.enter.prevent="open = !open"
                                        @keydown.space.prevent="open = !open"
                                        tabindex="0"
                                        role="button"
                                        :aria-expanded="open"
                                        aria-haspopup="listbox"
                                    >
                                        <flux:input
                                            wire:model="gender"
                                            x-model="gender"
                                            name="gender"
                                            class:input="border rounded-tl-none rounded-bl-none rounded-tr-lg rounded-br-lg cursor-pointer"
                                            x-bind:readonly
                                            placeholder="Select a gender"
                                            x-bind:value="selected === ''
                                                ? ''
                                                : (selected === 'Other'
                                                    ? (customValue || 'Custom gender')
                                                    : selected)"
                                        />

                                        <!-- Right-side controls -->
                                        <div class="absolute right-3 inset-y-0 flex items-center gap-2">
                                            <!-- Clear button -->
                                            <flux:button
                                                x-show="selected !== ''"
                                                size="sm"
                                                variant="subtle"
                                                icon="x-mark"
                                                class="h-5 w-5"
                                                @click.stop="
                                                    selected = '';
                                                    customValue = '';
                                                    $wire.set('gender', '', true);
                                                "
                                            />

                                            <!-- Chevron -->
                                            <div class="h-5 w-5 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor"
                                                    class="h-5 w-5 text-gray-500 transition-transform duration-200"
                                                    :class="open ? 'rotate-180' : ''">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dropdown -->
                                    <div
                                        x-show="open"
                                        @click.outside="open = false"
                                        x-transition
                                        class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white ring-1 ring-black ring-opacity-5 rounded-md shadow-md"
                                    >

                                        <ul class="py-1" role="listbox">
                                            <li>
                                                <button
                                                    @click="selected = 'Male'; open = false"
                                                    class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                                    :class="selected === 'Male'
                                                        ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                                        : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                                >
                                                    Male
                                                    <flux:icon.check
                                                        x-show="selected === 'Male'"
                                                        class="w-4 h-4" />
                                                </button>
                                            </li>

                                            <li>
                                                <button
                                                    @click="selected = 'Female'; open = false"
                                                    class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                                    :class="selected === 'Female'
                                                        ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                                        : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                                >
                                                    Female
                                                    <flux:icon.check
                                                        x-show="selected === 'Female'"
                                                        class="w-4 h-4" />
                                                </button>
                                            </li>

                                            <li>
                                                <button
                                                    @click="selected = 'Not Mentioned'; open = false"
                                                    class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                                    :class="selected === 'Not Mentioned'
                                                        ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                                        : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                                >
                                                    Not Mentioned
                                                    <flux:icon.check
                                                        x-show="selected === 'Not Mentioned'"
                                                        class="w-4 h-4" />
                                                </button>
                                            </li>

                                            <li>
                                                <button
                                                    @click="selected = 'Other'; open = true"
                                                    class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                                    :class="selected === 'Other'
                                                        ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                                        : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                                >
                                                    Other
                                                    <flux:icon.check
                                                        x-show="selected === 'Other'"
                                                        class="w-4 h-4" />
                                                </button>
                                            </li>
                                        </ul>
                                        <!-- Custom input -->
                                        <template x-if="selected === 'Other'">
                                            <div class="px-4 pb-3">
                                                <flux:input
                                                    type="text"
                                                    x-model="customValue"
                                                    placeholder="Please specify"
                                                    @click.stop
                                                    clearable
                                                    @input="$wire.set('gender', customValue, true)"
                                                />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </flux:input.group>
                            <flux:error name="gender" />
                        </flux:field>


            <!-- Dropdown with "Civil Status" -->
            <flux:field>
                <flux:label>Civil Status</flux:label>
                <flux:description>The individual's civil status</flux:description>
                <flux:input.group>
                    <flux:input.group.prefix>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </flux:input.group.prefix>

                    <div
                        x-data="{
                            open: false,
                            selected: @entangle('civil_status').defer,
                            customValue: '',
                            get value() {
                                return this.selected === 'Other' ? this.customValue : this.selected;
                            }
                        }"
                        x-init="
                            $watch('value', (val) => {
                                civil_status = val;
                                $wire.set('civil_status', val, true);
                            });
                            Livewire.on('resetDropdown', () => {
                                selected = '';
                                customValue = '';
                                civil_status = '';
                                open = false;
                                $wire.set('civil_status', '', true);
                            });
                        "
                        class="relative w-full"
                    >

                        <!-- Trigger -->
                        <div
                            @click="open = !open"
                            @keydown.enter.prevent="open = !open"
                            @keydown.space.prevent="open = !open"
                            tabindex="0"
                            role="button"
                            :aria-expanded="open"
                            aria-haspopup="listbox"
                        >
                            <flux:input
                                wire:model="civil_status"
                                x-model="civil_status"
                                name="civil_status"
                                class:input="border rounded-tl-none rounded-bl-none rounded-tr-lg rounded-br-lg cursor-pointer"
                                readonly
                                placeholder="Select a civil status"
                                x-bind:value="selected === ''
                                    ? ''
                                    : (selected === 'Other'
                                        ? (customValue || 'Custom civil status')
                                        : selected)"
                            />

                            <!-- Right-side controls -->
                            <div class="absolute right-3 inset-y-0 flex items-center gap-2">
                                <!-- Clear button -->
                                <flux:button
                                    x-show="selected !== ''"
                                    size="sm"
                                    variant="subtle"
                                    icon="x-mark"
                                    class="h-5 w-5"
                                    @click.stop="
                                        selected = '';
                                        customValue = '';
                                        $wire.set('civil_status', '', true);
                                    "
                                />

                                <!-- Chevron -->
                                <div class="h-5 w-5 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor"
                                        class="h-5 w-5 text-gray-500 transition-transform duration-200"
                                        :class="open ? 'rotate-180' : ''">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown -->
                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition
                            class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white ring-1 ring-black ring-opacity-5 rounded-md shadow-md"
                        >
                            <ul class="py-1" role="listbox">
                                <li>
                                    <button
                                        @click="selected = 'Single'; open = false"
                                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                        :class="selected === 'Single'
                                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                    >
                                        Single
                                        <flux:icon.check
                                            x-show="selected === 'Single'"
                                            class="w-4 h-4" />
                                    </button>
                                </li>

                                <li>
                                    <button
                                        @click="selected = 'Married'; open = false"
                                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                        :class="selected === 'Married'
                                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                    >
                                        Married
                                        <flux:icon.check
                                            x-show="selected === 'Married'"
                                            class="w-4 h-4" />
                                    </button>
                                </li>

                                <li>
                                    <button
                                        @click="selected = 'Divorced'; open = false"
                                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                        :class="selected === 'Divorced'
                                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                    >
                                        Divorced
                                        <flux:icon.check
                                            x-show="selected === 'Divorced'"
                                            class="w-4 h-4" />
                                    </button>
                                </li>

                                <li>
                                    <button
                                        @click="selected = 'Other'; open = true"
                                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                        :class="selected === 'Other'
                                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                    >
                                        Other
                                        <flux:icon.check
                                            x-show="selected === 'Other'"
                                            class="w-4 h-4" />
                                    </button>
                                </li>
                            </ul>
                            <!-- Custom input -->
                            <template x-if="selected === 'Other'">
                                <div class="px-4 pb-3">
                                    <flux:input
                                        type="text"
                                        x-model="customValue"
                                        placeholder="Please specify"
                                        @click.stop
                                        clearable
                                        @input="$wire.set('civil_status', customValue, true)"
                                    />
                                </div>
                            </template>
                        </div>
                    </div>
            </flux:input.group>
            <flux:error name="civil_status" />
            </flux:field>


            <!-- Barangay -->
             <flux:field>
                <flux:label>Barangay</flux:label>
                <flux:description>The individual's barangay community</flux:description>
                <flux:input.group>
                    <flux:input.group.prefix>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479
                                3 3 0 0 0-4.682-2.72m.94 3.198.001.031
                                c0 .225-.012.447-.037.666A11.944 11.944 0 0 1
                                12 21c-2.17 0-4.207-.576-5.963-1.584A6.062
                                6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0
                                0-.941-3.197m0 0A5.995 5.995 0 0 0
                                12 12.75a5.995 5.995 0 0 0-5.058
                                2.772m0 0a3 3 0 0 0-4.681 2.72
                                8.986 8.986 0 0 0 3.74.477m.94-3.197
                                a5.971 5.971 0 0 0-.94 3.197M15
                                6.75a3 3 0 1 1-6 0 3 3 0 0 1
                                6 0Zm6 3a2.25 2.25 0 1 1-4.5
                                0 2.25 2.25 0 0 1 4.5 0Zm-13.5
                                0a2.25 2.25 0 1 1-4.5 0 2.25
                                2.25 0 0 1 4.5 0Z" />
                        </svg>
                    </flux:input.group.prefix>

                    <div
                        x-data="{
                            open: false,
                            selected: @entangle('barangay').defer,
                            customValue: '',
                            get value() {
                                return this.selected === 'Other' ? this.customValue : this.selected;
                            }
                        }"
                        x-init="
                            $watch('value', (val) => {
                                barangay = val;
                                $wire.set('barangay', val, true);
                            });
                            Livewire.on('resetDropdown', () => {
                                selected = '';
                                customValue = '';
                                barangay = '';
                                open = false;
                                $wire.set('barangay', '', true);
                            });
                        "
                        class="relative w-full"
                    >

                        <!-- Trigger -->
                        <div
                            @click="open = !open"
                            @keydown.enter.prevent="open = !open"
                            @keydown.space.prevent="open = !open"
                            tabindex="0"
                            role="button"
                            :aria-expanded="open"
                            aria-haspopup="listbox"
                        >
                            <flux:input
                                wire:model="barangay"
                                x-model="barangay"
                                name="barangay"
                                class:input="border rounded-tl-none rounded-bl-none rounded-tr-lg rounded-br-lg cursor-pointer"
                                readonly
                                placeholder="Select a barangay"
                                x-bind:value="selected === ''
                                    ? ''
                                    : (selected === 'Other'
                                        ? (customValue || 'Custom barangay')
                                        : selected)"
                            />

                            <!-- Right-side controls -->
                            <div class="absolute right-3 inset-y-0 flex items-center gap-2">
                                <!-- Clear button -->
                                <flux:button
                                    x-show="selected !== ''"
                                    size="sm"
                                    variant="subtle"
                                    icon="x-mark"
                                    class="h-5 w-5"
                                    @click.stop="
                                        selected = '';
                                        customValue = '';
                                        $wire.set('barangay', '', true);
                                    "
                                />

                                <!-- Chevron -->
                                <div class="h-5 w-5 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor"
                                        class="h-5 w-5 text-gray-500 transition-transform duration-200"
                                        :class="open ? 'rotate-180' : ''">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown -->
                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition
                            class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white ring-1 ring-black ring-opacity-5 rounded-md shadow-md"
                        >
                            <ul class="py-1" role="listbox">
                                <li>
                                    <button
                                        @click="selected = 'Labogon'; open = false"
                                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                        :class="selected === 'Labogon'
                                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                    >
                                        Labogon
                                        <flux:icon.check
                                            x-show="selected === 'Labogon'"
                                            class="w-4 h-4" />
                                    </button>
                                </li>

                                <li>
                                    <button
                                        @click="selected = 'Paknaan'; open = false"
                                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                        :class="selected === 'Paknaan'
                                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                    >
                                        Paknaan
                                        <flux:icon.check
                                            x-show="selected === 'Paknaan'"
                                            class="w-4 h-4" />
                                    </button>
                                </li>

                                <li>
                                    <button
                                        @click="selected = 'Basak'; open = false"
                                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                        :class="selected === 'Basak'
                                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                    >
                                        Basak
                                        <flux:icon.check
                                            x-show="selected === 'Basak'"
                                            class="w-4 h-4" />
                                    </button>
                                </li>

                                <li>
                                    <button
                                        @click="selected = 'Other'; open = true"
                                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                                        :class="selected === 'Other'
                                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                    >
                                        Other
                                        <flux:icon.check
                                            x-show="selected === 'Other'"
                                            class="w-4 h-4" />
                                    </button>
                                </li>
                            </ul>

                            <!-- Custom input -->
                            <template x-if="selected === 'Other'">
                                <div class="px-4 pb-3">
                                    <flux:input
                                        type="text"
                                        x-model="customValue"
                                        placeholder="Please specify"
                                        @click.stop
                                        clearable
                                        @input="$wire.set('barangay', customValue, true)"
                                    />
                                </div>
                            </template>
                        </div>
                    </div>
            </flux:input.group>
            <flux:error name="barangay" />
            </flux:field>

            <!-- Sitio -->
            <flux:field>
                <flux:label>Sitio</flux:label>

                <flux:description>The individual's sitio/purok</flux:description>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </flux:input.group.prefix>
                     <flux:input wire:model="sitio" id="sitio" x-model="sitio" type="text" name="sitio"
                              autocomplete="sitio" placeholder="Enter your sitio" clearable />
                </flux:input.group>

                <flux:error name="sitio" />
            </flux:field>

            <flux:field
                    x-data
                    x-init="$nextTick(() => {
                        if (!$refs.input._flatpickr) {
                            flatpickr($refs.input, {
                                dateFormat: 'Y-m-d',
                                defaultDate: birthdate || null
                            });
                        }
                    })"
                    class="relative w-full"
                    >
                <flux:label>Birth Date</flux:label>
                <flux:description>The individual's date of birth</flux:description>
                <flux:error name="birthdate" class="my-2" />
                <flux:error name="age" class="my-2" />
                <flux:input.group class="cursor-pointer">
                    <flux:input.group.prefix>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                        </svg>
                    </flux:input.group.prefix>
                    <flux:input
                        x-ref="input"
                        type="text"
                        wire:model="birthdate"
                        x-model="birthdate"
                        placeholder="Enter a birthdate"
                        clearable
                    />
                </flux:input.group>
            </flux:field>
    </div>



    <div
    x-data="{
        preview: @js(asset('images/avatar.png')),
        defaultPreview: @js(asset('images/avatar.png')), // store default
        showSpinner: false,
        isDropping: false,
        minDelay: 700,
        _url: null,
        _timer: null,

        setPreview(file) {
            if (!file) return;
            this.showSpinner = true;
            if (this._url) URL.revokeObjectURL(this._url);
            this._url = URL.createObjectURL(file);

            clearTimeout(this._timer);
            this._timer = setTimeout(() => {
                this.preview = this._url;
                this.showSpinner = false;
            }, this.minDelay);
        },

        handleDrop(e) {
            this.isDropping = false;
            const files = e.dataTransfer?.files;
            if (!files || !files.length) return;
            this.$refs.fileInput.files = files;
            this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            this.setPreview(files[0]);
        },

        handleChange(e) {
            const file = e.target.files?.[0];
            this.setPreview(file);
        },

        resetPreview() {
            // revoke temp URL
            if (this._url) URL.revokeObjectURL(this._url);
            this._url = null;

            // reset preview to default
            this.preview = this.defaultPreview;

            // clear file input
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
        }
    }"
    >
        <!-- Page 2 -->
    <div class="relative w-full h-full flex flex-col items-center justify-between p-2 gap-8"  x-show="currentPage === 2">

     <!-- Profile Picture (Preview + Drag & Drop + Timed Swap) -->
   <div
    x-init="
        window.addEventListener('reset-register-form', () => resetPreview())
    "
    class="w-2/4 aspect-square border border-black rounded-full overflow-hidden relative"
>

        <!-- Overlay spinner (Alpine) -->
        <div
            x-show="showSpinner"
            x-transition.opacity
            class="absolute inset-0 flex items-center justify-center bg-black/50 z-10"
        >
            <span class="h-10 w-10">
                <flux:icon.loading />
            </span>

        </div>

        <!-- Image preview (client-side URL) -->
        <img
            :src="preview"
            class="w-full h-full object-cover transition-opacity duration-500"
            alt="preview"
        >

        <!-- Dropzone layer -->
        <div
            @dragover.prevent="isDropping = true"
            @dragleave.prevent="isDropping = false"
            @drop.prevent="handleDrop($event)"
            class="group absolute inset-0 flex items-center justify-center cursor-pointer transition
                bg-transparent hover:bg-white/10"
            :class="{ 'border-blue-500 bg-blue-50/70': isDropping }"
            title="Drag & drop or click to browse"
        >
            <p class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-xs text-gray-700 bg-white/70 rounded px-2 py-1">
                Drag & drop here or click to browse
            </p>

            <!-- The ONLY file input (bind to Livewire + Alpine) -->
            <input
                x-ref="fileInput"
                type="file"
                wire:model="profile_pic"
                @change="handleChange($event)"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                accept="image/*"
            />
        </div>
    </div>

    {{-- Livewire validation error --}}
    <flux:error name="profile_pic" />

    <div class="relative border-box w-full flex flex-col gap-3 px-5 pb-8">

    <!-- Name -->
    <flux:field>
        <flux:label>Username</flux:label>

        <flux:description>Create a unique and catchy username</flux:description>

        <flux:input.group>
            <flux:input.group.prefix>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
            </flux:input.group.prefix>
            <flux:input wire:model="name" x-model="name" id="name" type="text" name="name"
                        autocomplete="name" placeholder="Enter your username" clearable />
        </flux:input.group>

        <flux:error name="name" />
    </flux:field>

    <!-- Email Address -->
    <flux:field>
        <flux:label>Email</flux:label>

        <flux:description>Email address including Gmail, Yahoo, etc.</flux:description>

        <flux:input.group>
            <flux:input.group.prefix>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd" d="M17.834 6.166a8.25 8.25 0 1 0 0 11.668.75.75 0 0 1 1.06 1.06c-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788 3.807-3.808 9.98-3.808 13.788 0A9.722 9.722 0 0 1 21.75 12c0 .975-.296 1.887-.809 2.571-.514.685-1.28 1.179-2.191 1.179-.904 0-1.666-.487-2.18-1.164a5.25 5.25 0 1 1-.82-6.26V8.25a.75.75 0 0 1 1.5 0V12c0 .682.208 1.27.509 1.671.3.401.659.579.991.579.332 0 .69-.178.991-.579.3-.4.509-.99.509-1.671a8.222 8.222 0 0 0-2.416-5.834ZM15.75 12a3.75 3.75 0 1 0-7.5 0 3.75 3.75 0 0 0 7.5 0Z" clip-rule="evenodd" />
                </svg>
            </flux:input.group.prefix>
            <flux:input wire:model="email" x-model="email" id="email" type="text" name="email"
                        autocomplete="email" placeholder="Enter your email" clearable />
        </flux:input.group>

        <flux:error name="email" />
    </flux:field>

    <!-- Contact Number -->
    <flux:field>
        <flux:label>Contact Number</flux:label>

        <flux:description>Your personal contact number e.g. 639690944474</flux:description>

        <flux:input.group>
            <flux:input.group.prefix>
                +63
            </flux:input.group.prefix>
            <flux:input
                wire:model="contact"
                x-model="contact"
                id="contact"
                type="text"
                name="contact"
                maxlength="10"
                inputmode="numeric"
                x-data
                x-on:input="$el.value = $el.value.replace(/\D/g, '')"
                placeholder="Enter your contact number"
                autocomplete="contact"
                clearable
                />
        </flux:input.group>

        <flux:error name="contact" />
    </flux:field>

    <!-- Password -->
    <flux:field>
            <flux:label>Password</flux:label>

            <flux:description>Create a unique and catchy password</flux:description>

            <flux:input.group>
                <flux:input.group.prefix>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                </flux:input.group.prefix>

                <flux:input
                    wire:model="password"
                    x-model="password"
                    id="password"
                    type="password"
                    class:input="hide-password-toggle"
                    placeholder="Enter your password"
                    viewable
                    clearable
                />

            </flux:input.group>
        <flux:error name="password" />
    </flux:field>

    <!-- Confirm Password -->
    <flux:field>
        <flux:label>Confirm Password</flux:label>

        <flux:description>Confirm the password you have inputted above</flux:description>

        <flux:input.group>
            <flux:input.group.prefix>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </flux:input.group.prefix>

            <flux:input
                wire:model="password_confirmation"
                x-model="password_confirmation"
                id="password_confirmation"
                type="password"
                class:input="hide-password-toggle"
                placeholder="Retype password"
                viewable
                clearable
            />

        </flux:input.group>
        <flux:error name="password_confirmation" />
    </flux:field>
        </div>
    </div>

     <!-- Page 3 -->
    <div class="w-full h-auto flex flex-col px-2" x-show="currentPage === 3">

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                            <!-- Personal Info Card -->
                            <div class="bg-white dark:bg-zinc-900 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <!-- Card Header -->
                                <div class="bg-blue-500 px-5 py-4 flex items-center justify-between">
                                    <h2 class="text-lg font-semibold text-white uppercase tracking-wide">Personal Information</h2>
                                    <img
                                        :src="preview"
                                        alt="Profile Picture"
                                        class="w-14 h-14 rounded-full object-cover shadow-lg border-2 border-white/40"
                                    />
                                </div>

                                <!-- Card Content -->
                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="(value, label) in {
                                        'First Name': first_name,
                                        'Middle Name': middle_name,
                                        'Last Name': last_name,
                                        'Suffix': suffix,
                                        'Gender': gender,
                                        'Civil Status': civil_status,
                                        'Barangay': barangay,
                                        'Sitio': sitio,
                                        'Birth Date': birthdate
                                    }" :key="label">
                                        <div class="grid grid-cols-2 px-4 py-2">
                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase" x-text="label"></div>
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-100" x-text="value || '—'"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Account Info Card -->
                            <div class="bg-white dark:bg-zinc-900 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <!-- Card Header -->
                                <div class="bg-blue-500 px-5 py-4">
                                    <h2 class="text-lg font-semibold text-white uppercase tracking-wide">Account Information</h2>
                                </div>

                                <!-- Card Content -->
                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="(value, label) in {
                                        'Username': name,
                                        'Email': email,
                                        'Contact': `+63${contact}`,
                                        'Password': '•'.repeat(password.length)
                                    }" :key="label">
                                        <div class="grid grid-cols-2 px-4 py-2">
                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase" x-text="label"></div>
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 break-all" x-text="value || '—'"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <flux:field variant="inline" class="bg-gray-50 dark:bg-zinc-800 px-4 py-3 border border-gray-200 dark:border-gray-700 shadow-sm">
                            <flux:checkbox
                                wire:model="agreed_terms"
                            />
                            <flux:label class="text-sm text-gray-700 dark:text-gray-300 leading-6">
                                I agree to the terms and conditions
                            </flux:label>
                            <flux:error name="agreed_terms" class="text-xs text-red-500 mt-1"/>
                        </flux:field>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                    <div class="flex gap-3 mt-4 p-2">
                        <!-- Step 1 Button -->
                        <flux:button
                            variant="primary"
                            x-show="currentPage == 1"
                            x-bind:disabled="!first_name || !middle_name || !last_name || !suffix || !gender || !civil_status || !barangay || !sitio || !birthdate || loadingStepOne"
                            wire:click="validateStepOne"
                            wire:loading.attr="disabled"
                            wire:target="validateStepOne"
                            class="hover:bg-mc_primary_color dark:hover:bg-blue-700 hover:text-white dark:hover:text-white bg-black text-white dark:bg-white dark:text-black w-full transition duration-300 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="validateStepOne">Next</span>
                            <flux:icon.loading wire:loading wire:target="validateStepOne" class="w-5 h-5 animate-spin mx-auto" />
                        </flux:button>


                        <!-- Step 2 Button -->
                        <flux:button
                            variant="primary"
                            x-show="currentPage == 2"
                            x-bind:disabled="!name || !email || !contact || !password || !password_confirmation || loadingStepTwo"
                            wire:click="validateStepTwo"
                            wire:loading.attr="disabled"
                            wire:target="validateStepTwo"
                            class="hover:bg-mc_primary_color dark:hover:bg-blue-700 hover:text-white dark:hover:text-white bg-black text-white dark:bg-white dark:text-black w-full transition duration-300 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="validateStepTwo">Next</span>
                            <flux:icon.loading wire:loading wire:target="validateStepTwo" class="w-5 h-5 animate-spin mx-auto" />
                        </flux:button>

                        <!-- Step 3 Register Button -->
                        <flux:button
                            variant="primary"
                            x-show="currentPage == 3"
                            wire:click="register"
                            class="w-full transition duration-300 ease-in-out hover:bg-mc_primary_color dark:hover:bg-blue-700 hover:text-white dark:hover:text-white bg-black text-white dark:bg-white dark:text-black"
                        >
                            <span class="flex items-center justify-center gap-2">
                                <flux:icon.check-badge />
                                <span>{{ __('Register') }}</span>
                            </span>
                        </flux:button>
                    </div>

                    <flux:modal name="confirm-exit" class="w-full">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Are you sure don't want to register?</flux:heading>
                            </div>
                            <div class="flex gap-3">
                                <flux:spacer />

                                <flux:modal.close>
                                    <flux:button
                                        icon="x-mark"
                                        variant="primary"
                                        class="hover:bg-gray-100"
                                        x-on:click="$flux.modal('confirm-exit').close();"
                                    >
                                        Cancel
                                    </flux:button>
                                </flux:modal.close>

                               <flux:button
                                    variant="primary"
                                    icon="check"
                                    class="bg-blue-600 hover:bg-blue-700 text-white"
                                    @click="
                                        currentPage = 1;
                                        const modalScroll = document.getElementById('modal-scroll');
                                        if (modalScroll) modalScroll.scrollTop = 0;

                                        $flux.modal('confirm-exit').close();

                                        stepOneValid = false;
                                        stepTwoValid = false;
                                        open = false;

                                        Livewire.dispatch('reset-register-form');
                                        Livewire.dispatch('resetDropdown');
                                        window.dispatchEvent(new CustomEvent('reset-register-form'));
                                        $root.querySelector('[x-data]').__x.$data.resetPreview();
                                    "
                                >
                                    Yes
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>
                </div>
            </div>
                </div>


