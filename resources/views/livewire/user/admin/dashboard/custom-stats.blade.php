<div x-data="{ openCreate: false, openCreateLiaison: false, confirmDeleteAllActivityLogs: false }"
     @close-all-modals.window = "openCreate = false; openCreateLiaison = false; confirmDeleteAllActivityLogs = false"
>

    <div class="flex flex-col w-full space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                        border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                        transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">

                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                            group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

                <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                            dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                    <x-heroicon-o-user class="h-8 w-8 text-blue-600 dark:text-blue-400"/>
                </div>

                <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Total Users</p>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">
                    {{ $totalUsers }}
                </p>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 -mt-1">Registered in the system</p>

                <div class="grid grid-cols-2 gap-3 mt-3 w-full">
                    <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                                border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                                transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-user-circle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Citizens</span>
                        </div>
                        <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">
                            {{ $citizenOnline }} online / {{ $citizenUsers }} total
                        </span>
                    </div>

                    <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                                border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                                transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-briefcase class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">HR Liaisons</span>
                        </div>
                        <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">
                            {{ $hrLiaisonOnline }} online / {{ $hrLiaisonUsers }} total
                        </span>
                    </div>
                </div>
            </div>

            <div class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-zinc-800 dark:to-zinc-900
                border border-indigo-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">

                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-indigo-200/20 to-transparent opacity-0
                            group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

                <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-indigo-200/50
                            dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
                    </svg>
                </div>

                <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Assignments</p>
                <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">
                    {{ $totalAssignments }}
                </p>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">All assignment reports processed</p>

                <div x-data="{ open: false }" class="mt-3 w-full">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-2 bg-indigo-100 dark:bg-zinc-700
                                rounded-lg text-sm font-medium text-indigo-700 dark:text-indigo-300
                                hover:bg-indigo-200 dark:hover:bg-zinc-600 transition focus:outline-none">
                        <span>Assignments by Department</span>
                        <svg :class="{ 'rotate-180': open }"
                            class="w-4 h-4 ml-2 transition-transform"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-collapse x-show="open"
                        class="mt-2 w-full bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 border border-indigo-200/40 dark:border-zinc-700">
                        <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                            @foreach($assignmentsByDepartment as $dept)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                                        {{ $dept['department_name'] }}
                                    </span>
                                    <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                        {{ $dept['total'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">

                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                            group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

                <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                            dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                    <x-heroicon-o-document-text class="h-8 w-8 text-blue-600 dark:text-blue-400"/>
                </div>

                <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Forms Collected</p>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">
                    {{ $totalGrievances + $totalFeedbacks }}
                </p>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 -mt-1">Total reports and feedbacks</p>

                <div class="grid grid-cols-2 gap-3 mt-3 w-full">
                    <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                                border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                                transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-blue-600 dark:text-blue-400"/>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reports</span>
                        </div>
                        <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">
                            {{ $totalGrievances }}
                        </span>
                    </div>

                    <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                                border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                                transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-oval-left class="w-5 h-5 text-blue-600 dark:text-blue-400"/>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Feedbacks</span>
                        </div>
                        <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">
                            {{ $totalFeedbacks }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">

            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-bolt class="h-8 w-8 text-blue-600 dark:text-blue-400"/>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Quick Actions</p>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 -mt-1">Manage your HR and Departments</p>

            <div class="flex flex-col sm:flex-row gap-3 mt-3 w-full justify-center">
                <button
                    @click="openCreateLiaison = true"
                    wire:loading.attr="disabled"
                    wire:target="createHrLiaison"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700
                        text-white text-sm font-medium rounded-xl shadow-sm transition-all duration-300">
                    <x-heroicon-o-user-plus class="w-5 h-5" />
                    <span wire:loading.remove wire:target="createHrLiaison">
                        <span class="flex gap-2 justify-center items-center">
                            <x-heroicon-o-plus class="w-4 h-4" />
                            <span>Add HR Liaison</span>
                        </span>
                    </span>
                    <span wire:loading wire:target="createHrLiaison">
                        Processing...
                    </span>
                </button>

                <button
                    wire:loading.attr="disabled"
                    wire:target="createDepartment"
                    @click="openCreate = true"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                        text-white text-sm font-medium rounded-xl shadow-sm transition-all duration-300">
                    <x-heroicon-o-building-office class="w-5 h-5" />
                    <span wire:loading.remove wire:target="createDepartment">
                        <span class="flex gap-2 justify-center items-center">
                            <x-heroicon-o-plus class="w-4 h-4" />
                            <span>Create Department</span>
                        </span>
                    </span>
                    <span wire:loading wire:target="createDepartment">
                        Processing...
                    </span>
                </button>

                <button
                    @click="confirmDeleteAllActivityLogs = true"
                    wire:loading.attr="disabled"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700
                        text-white text-sm font-medium rounded-xl shadow-sm transition-all duration-300">
                    <x-heroicon-o-trash class="w-5 h-5" />
                    <span wire:loading.remove wire:target="confirmDeleteAllActivityLogs">
                        Delete All Activity Logs
                    </span>
                    <span wire:loading wire:target="confirmDeleteAllActivityLogs">
                        Processing...
                    </span>
                </button>

            </div>
        </div>

    </div>

    <div x-show="openCreate" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-lg max-h-[90vh] overflow-y-auto flex flex-col">

            <header class="flex gap-2 items-center justify-start border border-gray-300 dark:border-zinc-800 sticky top-0 bg-white dark:bg-zinc-800 z-10 p-3">
                <x-heroicon-o-squares-plus class="w-6 h-6" />
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 m-0">
                    Create New Department
                </h3>
            </header>

            <div wire:loading.remove wire:target="resetFields">
                <div class="space-y-3 w-full p-6">
                    <flux:input type="text" wire:model.defer="newDepartment.department_name" placeholder="Department Name" clearable/>
                    <flux:error name="newDepartment.department_name" />

                    <flux:input type="text" wire:model.defer="newDepartment.department_code" placeholder="Department Code" clearable/>
                    <flux:error name="newDepartment.department_code" />

                    <flux:textarea wire:model.defer="newDepartment.department_description" placeholder="Department Description" clearable/>
                    <flux:error name="newDepartment.department_description" />

                    <x-select
                        name="newDepartment.is_active"
                        wire:model.defer="newDepartment.is_active"
                        placeholder="Select active status"
                        :options="['Inactive','Active']"
                    />
                    <flux:error name="newDepartment.is_active" />

                    <x-select
                        name="newDepartment.is_available"
                        wire:model.defer="newDepartment.is_available"
                        placeholder="Select availability status"
                        :options="['Yes','No']"
                    />

                    <flux:error name="newDepartment.is_available" />

                    <x-select
                        name="newDepartment.requires_hr_liaison"
                        wire:model.defer="newDepartment.requires_hr_liaison"
                        placeholder="Is HR Liaison required?"
                        :options="['Yes','No']"
                    />

                    <flux:error name="newDepartment.requires_hr_liaison" />

                    <div class="space-y-4 mt-4">

                        <!-- Department Profile Upload -->
                        <div x-data="{ uploading: false, progress: 0 }"
                            x-init="
                                $el.querySelector('input').addEventListener('livewire-upload-start', () => uploading = true);
                                $el.querySelector('input').addEventListener('livewire-upload-progress', (event) => { progress = event.detail.progress });
                                $el.querySelector('input').addEventListener('livewire-upload-finish', () => uploading = false);
                                $el.querySelector('input').addEventListener('livewire-upload-error', () => uploading = false);
                            "
                            class="w-full max-w-md">

                            <label class="block text-sm font-medium text-gray-700 mb-1">Department Profile</label>

                            <flux:input type="file" wire:model="create_department_profile" accept=".jpg,.jpeg,.png" />

                            <!-- Progress Bar -->
                            <div x-show="uploading" class="relative w-full bg-gray-200 rounded h-2 mt-2 overflow-hidden">
                                <div class="absolute top-0 left-0 h-2 bg-blue-500 transition-all" :style="'width: ' + progress + '%'"></div>
                            </div>
                            <span x-show="uploading" class="text-xs text-gray-700 mt-1" x-text="progress + '%'"></span>

                            <flux:error name="create_department_profile" />
                        </div>

                        <div x-data="{
                                categories: @entangle('grievanceCategories').defer,
                                addCategory(type) {
                                    this.categories[type].push('');
                                },
                                removeCategory(type, index) {
                                    this.categories[type].splice(index, 1);
                                }
                            }">

                            @foreach(['Complaint','Inquiry','Request'] as $type)
                                <div class="flex flex-col gap-2 mb-3">
                                    <label class="font-medium text-gray-700 dark:text-gray-200">{{ $type }} Categories</label>

                                    @foreach($grievanceCategories[$type] as $index => $category)
                                        <div class="flex flex-col gap-1 mt-2">
                                            <div class="flex gap-2">
                                                <flux:input type="text"
                                                            wire:model.defer="grievanceCategories.{{ $type }}.{{ $index }}"
                                                            placeholder="Enter category" />

                                                <button type="button" wire:click="removeCategory('{{ $type }}', {{ $index }})"
                                                        class="px-3 py-1 text-xs rounded-md border border-red-400 text-white bg-red-600 hover:bg-red-700
                                                            dark:bg-red-900 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                                    Remove
                                                </button>
                                            </div>

                                            <!-- Flux Error for this specific category -->
                                            <flux:error name="grievanceCategories.{{ $type }}.{{ $index }}" />
                                        </div>
                                    @endforeach

                                    <button type="button" wire:click="addCategory('{{ $type }}')"
                                            class="px-3 py-1 text-xs rounded-md border border-green-400 text-white bg-green-600 hover:bg-green-700
                                                dark:bg-green-900 dark:text-green-300 dark:border-green-600 dark:hover:bg-green-800">
                                        Add {{ $type }}
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <!-- Department Background Upload -->
                        <div x-data="{ uploading: false, progress: 0 }"
                            x-init="
                                $el.querySelector('input').addEventListener('livewire-upload-start', () => uploading = true);
                                $el.querySelector('input').addEventListener('livewire-upload-progress', (event) => { progress = event.detail.progress });
                                $el.querySelector('input').addEventListener('livewire-upload-finish', () => uploading = false);
                                $el.querySelector('input').addEventListener('livewire-upload-error', () => uploading = false);
                            "
                            class="w-full max-w-md">

                            <label class="block text-sm font-medium text-gray-700 mb-1">Department Background</label>

                            <flux:input type="file" wire:model="create_department_background" accept=".jpg,.jpeg,.png" />

                            <!-- Progress Bar -->
                            <div x-show="uploading" class="relative w-full bg-gray-200 rounded h-2 mt-2 overflow-hidden">
                                <div class="absolute top-0 left-0 h-2 bg-green-500 transition-all" :style="'width: ' + progress + '%'"></div>
                            </div>
                            <span x-show="uploading" class="text-xs text-gray-700 mt-1" x-text="progress + '%'"></span>

                            <flux:error name="create_department_background" />
                        </div>

                    </div>
                </div>

                <footer class="flex justify-end gap-2 border-t border-gray-300 dark:border-zinc-700 p-3 bg-white dark:bg-zinc-800 sticky bottom-0 z-10 shadow-inner">
                    <button @click="openCreate = false; $wire.resetFields();"
                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60">
                        Cancel
                    </button>
                    <button wire:click="createDepartment"
                            wire:loading.attr="disabled"
                            wire:target="create_department_profile, create_department_background, createDepartment"
                            class="px-3 py-1 text-xs rounded-md border border-blue-400 text-white bg-blue-600 hover:bg-blue-700
                                dark:bg-blue-900 dark:text-blue-300 dark:border-blue-600 dark:hover:bg-blue-800
                                disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="createDepartment">Save</span>
                        <span wire:loading wire:target="createDepartment">Processing...</span>
                    </button>
                </footer>
            </div>

            <div wire:loading wire:target="resetFields">
                <div class="w-full flex items-center justify-center gap-2 py-6">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>

        </div>
    </div>

    <div x-show="openCreateLiaison" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md max-h-[90vh] overflow-y-auto flex flex-col">

            <header class="flex gap-2 items-center justify-start border border-gray-300 dark:border-zinc-800 sticky top-0 bg-white dark:bg-zinc-800 z-10 p-3">
                <x-heroicon-o-user-plus class="w-6 h-6" />
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 m-0">
                    Add HR Liaison
                </h3>
            </header>

            <div wire:loading.remove wire:target="resetFields">
                <div class="space-y-3 w-full p-6">
                    <flux:input type="text" wire:model.defer="newLiaison.name" placeholder="Full Name" clearable/>
                    <flux:error name="newLiaison.name" />

                    <flux:input type="email" wire:model.defer="newLiaison.email" placeholder="Email Address" clearable/>
                    <flux:error name="newLiaison.email" />

                    <flux:input type="password" wire:model.defer="newLiaison.password" placeholder="Password" class:input="hide-password-toggle" viewable clearable/>
                    <flux:error name="newLiaison.password" />
                </div>

                <footer class="flex justify-end gap-2 border-t border-gray-300 dark:border-zinc-700 p-3 bg-white dark:bg-zinc-800 sticky bottom-0 z-10 shadow-inner">
                    <button @click="openCreateLiaison = false; $wire.resetFields();"
                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60">
                        Cancel
                    </button>
                    <button wire:click="createHrLiaison" wire:loading.attr="disabled"
                            class="px-3 py-1 text-xs rounded-md border border-green-400 text-white bg-green-600 hover:bg-green-700
                                dark:bg-green-900 dark:text-green-300 dark:border-green-600 dark:hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="createHrLiaison">Save</span>
                        <span wire:loading wire:target="createHrLiaison">Processing...</span>
                    </button>
                </footer>
            </div>

            <div wire:loading wire:target="resetFields">
                <div class="w-full flex items-center justify-center gap-2 py-6">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="confirmDeleteAllActivityLogs" x-transition.opacity class="fixed inset-0 bg-black/50 z-50"></div>

    <div x-show="confirmDeleteAllActivityLogs" x-transition.scale
        class="fixed inset-0 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg w-full max-w-md p-6 text-center space-y-5">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 mx-auto">
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Confirm Deletion</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Are you sure you want to delete all activity logs? This action cannot be undone.</p>

            <div wire:loading.remove wire:target="confirmDeleteAllActivityLogs" class="flex justify-center gap-3 mt-4">
                <button type="button" @click="confirmDeleteAllActivityLogs = false"
                    class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                    Cancel
                </button>
                <flux:button variant="danger" icon="trash" wire:click="confirmDeleteAllActivityLogs">
                    Yes, Delete
                </flux:button>
            </div>

            <div wire:loading wire:target="confirmDeleteAllActivityLogs">
                <div class="flex items-center justify-center gap-2 w-full">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>
    </div>
</div>

