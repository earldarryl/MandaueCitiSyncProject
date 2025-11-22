<div
    x-data="{ open: false, formIsValid: false }"
    @open-register-modal.window="open = true;"
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
                        contact: '',

                        name: '',
                        email: '',
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

                            <div class="w-full flex flex-col items-center justify-center select-none py-6">
                                <div class="relative w-full max-w-3xl px-8">
                                    <div class="relative flex items-center justify-between">
                                        <div
                                            class="absolute top-6 left-[0%] right-[0%] h-[3px] w-full bg-gray-200 dark:bg-zinc-700 rounded-full transform -translate-y-1/2"
                                        ></div>

                                        <div
                                            class="absolute top-6 left-[0%] right-[0%] h-[3px] w-full bg-blue-500 rounded-full transform -translate-y-1/2 transition-all duration-700 ease-in-out"
                                            :style="{
                                                width:
                                                    currentPage === 1
                                                        ? '0%'
                                                        : currentPage === 2
                                                        ? '50%'
                                                        : currentPage >= 3
                                                        ? '100%'
                                                        : '0%'
                                            }"
                                        ></div>

                                        <div class="relative flex w-full justify-between">
                                            <template x-for="(label, index) in ['Personal Info', 'Account Info', 'Summary']" :key="index">
                                                <div
                                                    class="flex flex-col relative z-10 transition-all duration-300"
                                                    :class="{
                                                        'items-start': index === 0,
                                                        'items-center': index === 1,
                                                        'items-end': index === 2
                                                    }"
                                                >
                                                    <div
                                                        class="flex items-center justify-center w-10 h-10 rounded-full font-semibold transition-all duration-500 ease-in-out"
                                                        :class="{
                                                            'bg-blue-500 text-white shadow-[0_0_12px_rgba(59,130,246,0.6)] scale-110': currentPage >= index + 1,
                                                            'bg-gray-300 text-gray-500 dark:bg-zinc-700 dark:text-gray-400': currentPage < index + 1
                                                        }"
                                                    >
                                                        <template x-if="currentPage > index + 1">
                                                            <flux:icon.check />
                                                        </template>

                                                        <template x-if="currentPage <= index + 1">
                                                            <span>
                                                                <template x-if="index === 0">
                                                                    <flux:icon.identification />
                                                                </template>
                                                                <template x-if="index === 1">
                                                                    <flux:icon.user />
                                                                </template>
                                                                <template x-if="index === 2">
                                                                    <flux:icon.document-magnifying-glass />
                                                                </template>
                                                            </span>
                                                        </template>
                                                    </div>

                                                    <span
                                                        class="mt-2 text-sm font-bold transition-colors text-center"
                                                        :class="currentPage >= index + 1 ? 'text-blue-500 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'"
                                                        x-text="label"
                                                    ></span>
                                                </div>
                                            </template>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                            <div class="relative p-4">
                                <div class="relative p-4 bg-gray-50 border border-gray-200 dark:border-none dark:bg-zinc-800 flex flex-col w-full h-full">
                                    <div class="relative flex flex-col w-full h-full">

                                    <flux:modal wire:model.self="showConfirmModal">
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

                                    <!-- Page 1 -->
                                    <div class="relative border-box w-full font-bold flex flex-col gap-4 px-5 pb-8 gap-4" x-show="currentPage === 1">
                                            <flux:field>
                                                <div class="flex flex-col gap-2">
                                                    <flux:label class="flex gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="text-mc_primary_color dark:text-blue-500" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="square" stroke-width="2" d="M12 19V4.5h-.5L9.5 6"/></svg>
                                                        <span>First Name</span>
                                                    </flux:label>

                                                    <flux:description>The first part of your full name</flux:description>

                                                    <flux:input.group>
                                                        <flux:input wire:model="first_name" x-model="first_name" id="first_name" type="text" name="first_name"
                                                                autocomplete="first_name" placeholder="Enter your first name" clearable />
                                                    </flux:input.group>
                                                </div>

                                                <flux:error name="first_name" />
                                            </flux:field>

                                            <flux:field>
                                                <div class="flex flex-col gap-2">
                                                    <flux:label class="flex gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="text-mc_primary_color dark:text-blue-500" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="square" stroke-width="2" d="M16 19H8v-2.024a1 1 0 0 1 .37-.776l6.128-4.98A4.07 4.07 0 0 0 16 8.065V8a4 4 0 0 0-8 0"/></svg>
                                                        <span>Middle Name</span>
                                                    </flux:label>

                                                    <flux:description>The middle part of your full name</flux:description>

                                                    <flux:input.group>
                                                        <flux:input wire:model="middle_name" x-model="middle_name" id="middle_name" type="text" name="middle_name"
                                                                autocomplete="middle_name" placeholder="Enter your middle name" clearable />
                                                    </flux:input.group>

                                                    <flux:error name="middle_name" />
                                                </div>

                                            </flux:field>

                                            <flux:field>
                                                <div class="flex flex-col gap-2">
                                                    <flux:label class="flex gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="text-mc_primary_color dark:text-blue-500" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="square" stroke-width="2" d="M8.5 5h7v1.5l-5 3.5v1H12a4 4 0 0 1 0 8h-.5a4 4 0 0 1-4-4"/></svg>
                                                        <span>Last Name</span>
                                                    </flux:label>

                                                    <flux:description>The last part of your full name</flux:description>

                                                    <flux:input.group>
                                                        <flux:input wire:model="last_name" x-model="last_name" id="last_name" type="text" name="last_name"
                                                                autocomplete="last_name" placeholder="Enter your last name" clearable />
                                                    </flux:input.group>
                                                </div>

                                                <flux:error name="last_name" />
                                            </flux:field>

                                            <flux:field>
                                                <div class="flex flex-col gap-2">
                                                    <flux:label class="flex gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="text-mc_primary_color dark:text-blue-500" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="square" stroke-width="2" d="M14.5 19v-4m0 0H7v-1l6.5-9h1zm0 0h2"/></svg>
                                                        <span>Suffix</span>
                                                    </flux:label>
                                                    <flux:description>Only if you have the same name within a family (Optional)</flux:description>

                                                    <flux:input.group>

                                                                <x-searchable-select
                                                                    name="suffix"
                                                                    placeholder="Select a suffix"
                                                                    :options="['N/A', 'Jr.', 'Sr.', 'II', 'III', 'IV', 'V']"
                                                                />

                                                    </flux:input.group>
                                                </div>

                                        <flux:error name="suffix" />
                                        </flux:field>

                                        <flux:field>
                                            <div class="flex flex-col gap-2">
                                                <flux:label class="flex gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="text-mc_primary_color dark:text-blue-500" viewBox="0 0 32 32"><path fill="currentColor" d="M22 3v2h3.563l-3.375 3.406A6.96 6.96 0 0 0 18 7c-1.87 0-3.616.74-4.938 2.063a6.94 6.94 0 0 0 .001 9.875c.87.87 1.906 1.495 3.062 1.812c.114-.087.242-.178.344-.28a3.45 3.45 0 0 0 .874-1.532a4.9 4.9 0 0 1-2.875-1.407C13.524 16.588 13 15.336 13 14s.525-2.586 1.47-3.53C15.412 9.523 16.664 9 18 9s2.587.525 3.53 1.47A4.96 4.96 0 0 1 23 14c0 .865-.245 1.67-.656 2.406c.096.516.156 1.058.156 1.594q0 .749-.125 1.47c.2-.163.378-.348.563-.532C24.26 17.614 25 15.87 25 14c0-1.53-.504-2.984-1.406-4.188L27 6.438V10h2V3zm-6.125 8.25c-.114.087-.242.178-.344.28c-.432.434-.714.96-.874 1.533c1.09.14 2.085.616 2.875 1.406c.945.943 1.47 2.195 1.47 3.53s-.525 2.586-1.47 3.53C16.588 22.477 15.336 23 14 23s-2.587-.525-3.53-1.47A4.95 4.95 0 0 1 9 18c0-.865.245-1.67.656-2.406A9 9 0 0 1 9.5 14q0-.748.125-1.47c-.2.163-.377.348-.563.533C7.742 14.384 7 16.13 7 18c0 1.53.504 2.984 1.406 4.188L6.72 23.875l-2-2l-1.44 1.406l2 2l-2 2l1.44 1.44l2-2l2 2l1.405-1.44l-2-2l1.688-1.686A6.93 6.93 0 0 0 14 25c1.87 0 3.616-.74 4.938-2.063C20.26 21.616 21 19.87 21 18s-.74-3.614-2.063-4.938c-.87-.87-1.906-1.495-3.062-1.812"/></svg>
                                                    <span>Gender</span>
                                                </flux:label>
                                                <flux:description>The individual's gender identity</flux:description>
                                                <flux:input.group>

                                                <x-searchable-select
                                                    name="gender"
                                                    placeholder="Select a gender"
                                                    :options="['Male', 'Female', 'Prefer not to say']"
                                                    />

                                                </flux:input.group>
                                            </div>

                                            <flux:error name="gender" />
                                        </flux:field>


                                        <flux:field>
                                            <div class="flex flex-col gap-2">
                                                <flux:label class="flex gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                    </svg>
                                                    <span>Civil Status</span>
                                                </flux:label>
                                                <flux:description>The individual's civil status</flux:description>
                                                <flux:input.group>

                                                        <x-searchable-select
                                                            name="civil_status"
                                                            placeholder="Select a civil status"
                                                            :options="[
                                                                'Single',
                                                                'Married',
                                                                'Widowed',
                                                                'Separated',
                                                                'Annulled',
                                                                'Divorced',
                                                                'Live-in',
                                                                'Prefer not to say'
                                                            ]"
                                                        />

                                            </flux:input.group>
                                            </div>

                                        <flux:error name="civil_status" />
                                        </flux:field>


                                        <flux:field>
                                            <div class="flex flex-col gap-2">
                                                <flux:label class="flex gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
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
                                                    <span>Barangay</span>
                                                </flux:label>
                                                <flux:description>The individual's barangay community</flux:description>
                                                <flux:input.group>

                                                    <x-searchable-select
                                                        name="barangay"
                                                        placeholder="Select a barangay"
                                                        :options="[
                                                            'Alang-alang',
                                                            'Bakilid',
                                                            'Banilad',
                                                            'Basak',
                                                            'Cabancalan',
                                                            'Cambaro',
                                                            'Canduman',
                                                            'Casili',
                                                            'Centro',
                                                            'Cubacub',
                                                            'Guizo',
                                                            'Ibabao-Estancia',
                                                            'Jagobiao',
                                                            'Labogon',
                                                            'Looc',
                                                            'Maguikay',
                                                            'Mantuyong',
                                                            'Opao',
                                                            'Paknaan',
                                                            'Pagsabungan',
                                                            'Subangdaku',
                                                            'Tabok',
                                                            'Tawason',
                                                            'Tingub',
                                                            'Tipolo',
                                                            'Umapad',
                                                            'Casuntingan'
                                                        ]"
                                                    />

                                            </flux:input.group>
                                            </div>

                                        <flux:error name="barangay" />
                                        </flux:field>

                                        <flux:field>
                                            <div class="flex flex-col gap-2">
                                                 <flux:label class="flex gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                                    </svg>
                                                    <span>Sitio</span>
                                                </flux:label>

                                                <flux:description>The individual's sitio/purok</flux:description>

                                                <flux:input.group>
                                                    <flux:input wire:model="sitio" id="sitio" x-model="sitio" type="text" name="sitio"
                                                            autocomplete="sitio" placeholder="Enter your sitio" clearable />
                                                </flux:input.group>
                                            </div>

                                            <flux:error name="sitio" />
                                        </flux:field>

                                       <flux:field>
                                            <div class="flex flex-col gap-2">
                                                <flux:label class="flex gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                                                    </svg>
                                                    <span>Birth Date</span>
                                                </flux:label>

                                                <flux:description>The individual's date of birth</flux:description>

                                                <flux:input.group>
                                                    <div
                                                        x-data
                                                        x-init="$nextTick(() => {
                                                            const setupFlatpickr = () => {
                                                                const isDark = document.documentElement.classList.contains('dark');

                                                                if (!$refs.input._flatpickr) {
                                                                    flatpickr($refs.input, {
                                                                        dateFormat: 'Y-m-d',
                                                                        defaultDate: birthdate || null,
                                                                        onReady: function(selectedDates, dateStr, instance) {
                                                                            applyDarkMode(instance, isDark);
                                                                        },
                                                                    });
                                                                } else {
                                                                    applyDarkMode($refs.input._flatpickr, isDark);
                                                                }
                                                            };

                                                            const applyDarkMode = (instance, isDark) => {
                                                                const calendar = instance.calendarContainer;
                                                                if (!calendar) return;
                                                                calendar.classList.toggle('flatpickr-dark', isDark);
                                                            };

                                                            setupFlatpickr();

                                                            const observer = new MutationObserver(() => {
                                                                const isDark = document.documentElement.classList.contains('dark');
                                                                if ($refs.input._flatpickr) {
                                                                    applyDarkMode($refs.input._flatpickr, isDark);
                                                                }
                                                            });

                                                            observer.observe(document.documentElement, {
                                                                attributes: true,
                                                                attributeFilter: ['class']
                                                            });
                                                        })"
                                                        class="relative w-full cursor-pointer"
                                                    >
                                                        <flux:input
                                                            x-ref="input"
                                                            type="text"
                                                            wire:model="birthdate"
                                                            class="cursor-pointer select-none"
                                                            x-model="birthdate"
                                                            placeholder="Enter a birthdate"
                                                            readonly
                                                        />
                                                    </div>
                                                </flux:input.group>

                                            </div>

                                            <flux:error name="birthdate" />
                                            <flux:error name="age" />
                                        </flux:field>

                                        <flux:field>
                                            <div class="flex flex-col gap-2">
                                                <flux:label class="flex gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                                    </svg>
                                                    <span>Contact Number</span>
                                                </flux:label>

                                                <flux:description>Your personal contact number (e.g. 917-456-7890)</flux:description>

                                                <flux:input.group
                                                    x-data="{
                                                        formatPhone(event) {
                                                            let value = event.target.value.replace(/\D/g, '');
                                                            if (value.length > 10) value = value.slice(0, 10);
                                                            if (value.length > 6)
                                                                event.target.value = `${value.slice(0,3)}-${value.slice(3,6)}-${value.slice(6,10)}`;
                                                            else if (value.length > 3)
                                                                event.target.value = `${value.slice(0,3)}-${value.slice(3,6)}`;
                                                            else event.target.value = value;
                                                        }
                                                    }"
                                                >
                                                    <flux:input.group.prefix class="flex gap-2 justify-center items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 72 72">
                                                            <path fill="#1e50a0" d="M5 17h62v38H5z" />
                                                            <path fill="#d22f27" d="M5 36h62v19H5z" />
                                                            <path fill="#fff" d="M37 36L5 55V17z" />
                                                        </svg>
                                                        <span>+63</span>
                                                    </flux:input.group.prefix>

                                                    <flux:input
                                                        wire:model="contact"
                                                        x-model="contact"
                                                        id="contact_number"
                                                        type="text"
                                                        name="contact"
                                                        maxlength="12"
                                                        inputmode="numeric"
                                                        placeholder="912-345-6789"
                                                        autocomplete="tel"
                                                        x-on:input="formatPhone($event)"
                                                        clearable
                                                    />
                                                </flux:input.group>
                                            </div>

                                            <flux:error name="contact" />
                                        </flux:field>

                                    </div>

                                    <!-- Page 2 -->
                                    <div class="relative w-full h-full font-bold flex flex-col items-center justify-between p-2 gap-8"  x-show="currentPage === 2">

                                        <div class="relative border-box w-full flex flex-col gap-3 px-5 pb-8">

                                            <flux:field>
                                                <div class="flex flex-col gap-2">
                                                    <flux:label class="flex gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                                        </svg>
                                                        <span>Name</span>
                                                    </flux:label>

                                                    <flux:description>Create a unique and catchy username</flux:description>

                                                    <flux:input.group>
                                                        <flux:input wire:model="name" x-model="name" id="name" type="text" name="name"
                                                                    autocomplete="name" placeholder="Enter your username" clearable />
                                                    </flux:input.group>
                                                </div>

                                                <flux:error name="name" />
                                            </flux:field>

                                            <flux:field>
                                                <div class="flex flex-col gap-2">
                                                    <flux:label class="flex gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
                                                            <path fill-rule="evenodd" d="M17.834 6.166a8.25 8.25 0 1 0 0 11.668.75.75 0 0 1 1.06 1.06c-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788 3.807-3.808 9.98-3.808 13.788 0A9.722 9.722 0 0 1 21.75 12c0 .975-.296 1.887-.809 2.571-.514.685-1.28 1.179-2.191 1.179-.904 0-1.666-.487-2.18-1.164a5.25 5.25 0 1 1-.82-6.26V8.25a.75.75 0 0 1 1.5 0V12c0 .682.208 1.27.509 1.671.3.401.659.579.991.579.332 0 .69-.178.991-.579.3-.4.509-.99.509-1.671a8.222 8.222 0 0 0-2.416-5.834ZM15.75 12a3.75 3.75 0 1 0-7.5 0 3.75 3.75 0 0 0 7.5 0Z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span>Email</span>
                                                    </flux:label>

                                                    <flux:description>Email address including Gmail, Yahoo, etc.</flux:description>

                                                    <flux:input.group>
                                                        <flux:input wire:model="email" x-model="email" id="email" type="text" name="email"
                                                                    autocomplete="email" placeholder="Enter your email" clearable />
                                                    </flux:input.group>
                                                </div>


                                                <flux:error name="email" />
                                            </flux:field>

                                            <flux:field>
                                                    <div class="flex flex-col gap-2">
                                                        <flux:label class="flex gap-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                                            </svg>
                                                            <span>Password</span>
                                                        </flux:label>

                                                        <flux:description>Create a unique and catchy password</flux:description>

                                                        <flux:input.group>

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
                                                    </div>

                                                <flux:error name="password" />
                                            </flux:field>

                                            <flux:field>
                                                <div class="flex flex-col gap-2">
                                                    <flux:label class="flex gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-mc_primary_color dark:text-blue-500">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                        <span>Confirm Password</span>
                                                    </flux:label>

                                                    <flux:description>Confirm the password you have inputted above</flux:description>

                                                    <flux:input.group>

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
                                                </div>

                                                <flux:error name="password_confirmation" />
                                            </flux:field>

                                        </div>
                                    </div>

                                    <!-- Page 3 -->
                                    <div class="w-full h-auto font-bold flex flex-col" x-show="currentPage === 3">

                                        <div class="grid grid-cols-1 gap-2">

                                            <div class="bg-white dark:bg-zinc-900 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                <div class="flex gap-2 justify-start bg-blue-500 px-5 py-4 flex items-center justify-between">
                                                    <x-heroicon-o-user class="w-6 h-6 text-white"/>
                                                    <h2 class="text-lg font-semibold text-white uppercase tracking-wide">Personal Information</h2>
                                                </div>

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
                                                        'Birth Date': birthdate,
                                                        'Contact Number': contact ? `+63${contact}` : '—',
                                                    }" :key="label">
                                                        <div class="grid grid-cols-2 px-4 py-2">
                                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase" x-text="label"></div>
                                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-100" x-text="value || '—'"></div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            <div class="bg-white dark:bg-zinc-900 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                <div class="flex gap-2 justify-start bg-blue-500 px-5 py-4">
                                                    <x-heroicon-o-user-circle class="w-6 h-6 text-white"/>
                                                    <h2 class="text-lg font-semibold text-white uppercase tracking-wide">Account Information</h2>
                                                </div>

                                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                                    <template x-for="(value, label) in {
                                                        'Username': name,
                                                        'Email': email,
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

                                        <flux:field
                                            x-data
                                            @check-terms-error.window="
                                                const modalScroll = document.getElementById('modal-scroll');
                                                if (modalScroll) {
                                                    modalScroll.scrollTo({ top: modalScroll.scrollHeight, behavior: 'smooth' });
                                                }
                                            "
                                            variant="inline"
                                            class="bg-gray-50 dark:bg-zinc-800 px-4 py-3 border border-gray-200 dark:border-gray-300 rounded-sm shadow-sm mt-4"
                                        >
                                            <flux:checkbox wire:model="agreed_terms" />
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

                        <div class="flex gap-3 mt-4 p-2 w-full items-center justify-center">

                            <!-- Loading Dots -->
                            <div wire:loading wire:target="validateStepOne, validateStepTwo, register">
                                <div class="w-full flex items-center justify-center gap-2 p-3">
                                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                </div>
                            </div>

                            <!-- Step 1 Button -->
                            <flux:button
                                variant="primary"
                                x-show="currentPage == 1"
                                wire:click="validateStepOne"
                                wire:loading.attr="disabled"
                                wire:target="validateStepOne"
                                wire:loading.remove
                                class="w-full transition duration-300 border !border-black hover:!bg-gray-100 dark:hover:!bg-zinc-600 !bg-white dark:!bg-zinc-800 dark:!border-white dark:!text-white ease-in-out"
                            >
                                <span wire:loading.remove wire:target="validateStepOne">
                                    <span class="flex items-center justify-center gap-2">
                                        <span><flux:icon.forward/></span>
                                        <span>Next</span>
                                    </span>
                                </span>
                            </flux:button>


                            <!-- Step 2 Button -->
                            <flux:button
                                variant="primary"
                                x-show="currentPage == 2"
                                wire:click="validateStepTwo"
                                wire:loading.attr="disabled"
                                wire:target="validateStepTwo"
                                wire:loading.remove
                                class="w-full transition duration-300 border !border-black hover:!bg-gray-100 dark:hover:!bg-zinc-600 !bg-white dark:!bg-zinc-800 dark:!border-white dark:!text-white ease-in-out"
                            >
                                <span wire:loading.remove wire:target="validateStepTwo">
                                    <span class="flex items-center justify-center gap-2">
                                        <span><flux:icon.forward/></span>
                                        <span>Next</span>
                                    </span>
                                </span>
                            </flux:button>

                            <!-- Step 3 Register Button -->
                            <flux:button
                                variant="primary"
                                x-show="currentPage == 3"
                                wire:click="register"
                                wire:loading.remove
                                class="w-full transition duration-300 border !border-black hover:!bg-gray-100 dark:hover:!bg-zinc-600 !bg-white dark:!bg-zinc-800 dark:!border-white dark:!text-white ease-in-out"
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
