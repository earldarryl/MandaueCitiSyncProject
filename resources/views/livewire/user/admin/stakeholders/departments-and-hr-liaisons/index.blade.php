    <div class="p-6 relative w-full"
         x-data="{ openCreate: false, openCreateLiaison: false }"
         @close-all-modals.window = "openCreate = false; openCreateLiaison = false"
         data-component="admin-departments-and-hr-liaisons-index"
         data-wire-id="{{ $this->id() }}"
    >
        <div class="relative">

            <div
                x-data="{
                    openStats: $store.sidebar.screen >= 768,
                    updateStatsVisibility() {
                        this.openStats = $store.sidebar.screen >= 768;
                    }
                }"
                x-init="updateStatsVisibility(); window.addEventListener('resize', () => updateStatsVisibility())"
                class="relative w-full h-auto flex flex-col"
            >
                <div class="flex justify-center items-center mb-4">
                    <button
                        @click="openStats = !openStats"
                        class="flex items-center justify-center gap-2 px-4 py-2 rounded-full border border-gray-300 dark:border-zinc-600
                            bg-white dark:bg-zinc-800 shadow-sm hover:shadow-md transition-all duration-300"
                    >
                        <flux:icon.chevron-down
                            :class="openStats ? 'rotate-180 text-blue-500' : 'text-gray-600'"
                            class="h-5 w-5 transition-transform duration-300"
                        />
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                            <template x-if="!openStats"><span>Show Statistics</span></template>
                            <template x-if="openStats"><span>Hide Statistics</span></template>
                        </span>
                    </button>
                </div>

                <div
                    x-show="openStats"
                    x-collapse
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-400"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full flex flex-col h-auto"
                >
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">

                        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                                    border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                    transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <x-heroicon-o-user-group class="h-8 w-8 text-blue-600 dark:text-blue-400"/>
                            </div>

                            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Total HR Liaisons</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $totalHrLiaisons }}</p>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 -mt-1">Registered in the system</p>

                            <div class="grid grid-cols-2 gap-3 mt-3 w-full">
                                <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                                            border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                                            transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-user-circle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Assigned</span>
                                    </div>
                                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">{{ $assignedHrLiaisons }}</span>
                                </div>

                                <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                                            border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                                            transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-user-minus class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Unassigned</span>
                                    </div>
                                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">{{ $unassignedHrLiaisons }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="group relative bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-zinc-800 dark:to-zinc-900
                                    border border-cyan-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                    transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-cyan-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>

                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-cyan-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.clock class="h-8 w-8 text-cyan-600 dark:text-cyan-400" />
                            </div>

                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Total Activity Hours</p>
                            <p class="relative text-3xl font-bold text-cyan-600 dark:text-cyan-400 tracking-tight">{{ $totalLiaisonHours }}</p>
                        </div>

                        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                                    border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                    transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>

                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.building-office class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                            </div>

                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Departments</p>
                            <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $totalDepartments }}</p>
                        </div>

                        <div class="group relative bg-gradient-to-br from-purple-50 to-purple-100 dark:from-zinc-800 dark:to-zinc-900
                                    border border-purple-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                    transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>

                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-purple-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.user-plus class="h-8 w-8 text-purple-600 dark:text-purple-400" />
                            </div>

                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Recently Added</p>
                            <p class="relative text-3xl font-bold text-center text-purple-600 dark:text-purple-400 tracking-tight">
                                {{ $recentDepartment ? $recentDepartment->department_name : 'N/A' }}
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="w-full h-full p-6">
                <div class="flex flex-col items-center justify-center gap-4 mb-6">
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-5 gap-3 mb-2 w-full px-4">
                        <x-filter-select
                            name="filterDate"
                            wire:model="filterDate"
                            placeholder="Date"
                            :options="['Show All', 'Today', 'Yesterday', 'This Week', 'This Month', 'This Year']"
                        />

                        <x-filter-select
                            name="filterActive"
                            wire:model="filterActive"
                            placeholder="Department Status"
                            :options="['All', 'Active', 'Inactive']"
                        />

                        <x-filter-select
                            name="filterAvailability"
                            wire:model="filterAvailability"
                            placeholder="Availability"
                            :options="['All', 'Yes', 'No']"
                        />

                        <x-filter-select
                            name="filterHRStatus"
                            wire:model="filterHRStatus"
                            placeholder="HR Liaisons Status"
                            :options="['All', 'Active', 'Inactive']"
                        />

                        <x-filter-select
                            name="nameStartsWith"
                            wire:model="nameStartsWith"
                            placeholder="Filter by Letter"
                            :options="['All','A','B','C','D','E','F','G','H','I','J','K','L','M',
                                        'N','O','P','Q','R','S','T','U','V','W','X','Y','Z']"
                        />
                    </div>

                    <div class="flex justify-center w-full px-3">
                        <button
                            wire:click="applyFilters"
                            class="flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white w-full font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
                            <flux:icon.adjustments-horizontal class="w-4 h-4" />
                            <span>Apply Filters</span>
                        </button>
                    </div>
                </div>

            <div class="flex w-full mb-4">
                <div class="relative w-full font-bold">
                    <label for="search" class="sr-only">Search</label>

                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                        </div>

                        <input
                            type="text"
                            id="search"
                            wire:model.defer="searchInput"
                            wire:keydown.enter="applySearch"
                            placeholder="Search deparments..."
                            class="block w-full p-4 ps-10 pe-28 text-sm text-gray-900 border border-gray-300 rounded-lg
                                bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                dark:bg-zinc-800 dark:border-gray-600 dark:placeholder-gray-400
                                dark:text-white dark:focus:outline-none dark:focus:ring-2 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                        />

                        <button type="button" wire:click="clearSearch"
                            class="absolute inset-y-0 right-28 flex items-center justify-center text-gray-500 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>

                        <button type="button" wire:click="applySearch"
                            class="absolute inset-y-0 right-0 my-auto inline-flex items-center justify-center gap-2
                                px-4 py-2 text-sm font-semibold rounded-r-xl
                                text-white bg-gradient-to-r from-blue-600 to-blue-700
                                hover:from-blue-700 hover:to-blue-800
                                shadow-sm hover:shadow-md transition-all duration-200">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                            <span>Search</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mb-3 gap-2">
                <button
                    wire:loading.attr="disabled"
                    wire:target="createDepartment"
                    @click="openCreate = true; $wire.resetFields();"
                    class="px-4 py-2 rounded-md border border-blue-400 text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-600 dark:hover:bg-blue-900/60"
                >
                    <span wire:loading.remove wire:target="createDepartment">
                        <span class="flex gap-2 justify-center items-center">
                            <x-heroicon-o-building-office class="w-5 h-5" />
                            <x-heroicon-o-plus class="w-4 h-4" />
                            <span>Create Department</span>
                        </span>
                    </span>
                    <span wire:loading wire:target="createDepartment">
                        Processing...
                    </span>
                </button>

                <button
                    @click="openCreateLiaison = true; $wire.resetFields();"
                    wire:loading.attr="disabled"
                    wire:target="createHrLiaison"
                    class="px-4 py-2 rounded-md border border-green-400 text-green-700 bg-green-50 hover:bg-green-100 dark:bg-green-900/40 dark:text-green-300 dark:border-green-600 dark:hover:bg-green-900/60"
                >
                    <span wire:loading.remove wire:target="createHrLiaison">
                        <span class="flex gap-2 justify-center items-center">
                            <x-heroicon-o-user-plus class="w-5 h-5" />
                            <x-heroicon-o-plus class="w-4 h-4" />
                            <span>Add HR Liaison</span>
                        </span>
                    </span>
                    <span wire:loading wire:target="createHrLiaison">
                        Processing...
                    </span>
                </button>
            </div>

                <div wire:loading.remove wire:target="previousPage, nextPage, gotoPage, applySearch, clearSearch">
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold">Profile</th>

                                    <th wire:click="sortBy('department_name')" class="px-6 py-3 cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span>Department Name</span>
                                            <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                                @if($sortField === 'department_name')
                                                    @if($sortDirection === 'asc')
                                                        <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @else
                                                        <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @endif
                                                @else
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                                @endif
                                            </span>
                                        </div>
                                    </th>

                                    <th wire:click="sortBy('department_code')" class="px-6 py-3 cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span>Code</span>
                                            <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                                @if($sortField === 'department_code')
                                                    @if($sortDirection === 'asc')
                                                        <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @else
                                                        <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @endif
                                                @else
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                                @endif
                                            </span>
                                        </div>
                                    </th>

                                    <th wire:click="sortBy('is_active')" class="px-6 py-3 cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span>Active Status</span>
                                            <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                                @if($sortField === 'is_active')
                                                    @if($sortDirection === 'asc')
                                                        <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @else
                                                        <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @endif
                                                @else
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                                @endif
                                            </span>
                                        </div>
                                    </th>

                                    <th wire:click="sortBy('is_available')" class="px-6 py-3 cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span>Availability Status</span>
                                            <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                                @if($sortField === 'is_available')
                                                    @if($sortDirection === 'asc')
                                                        <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @else
                                                        <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @endif
                                                @else
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                                @endif
                                            </span>
                                        </div>
                                    </th>

                                    <th wire:click="sortBy('hr_liaisons_count')" class="px-6 py-3 cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span>HR Liaisons</span>
                                            <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                                @if($sortField === 'hr_liaisons_count')
                                                    @if($sortDirection === 'asc')
                                                        <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @else
                                                        <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                    @endif
                                                @else
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                                @endif
                                            </span>
                                        </div>
                                    </th>

                                    <th class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                @forelse($departments as $department)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition" wire:key="department-{{ $department->department_id }}">
                                    <td class="px-6 py-4">
                                        <img src="{{ $department->department_profile_url ?? asset('images/default-dept.png') }}"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-300 dark:border-zinc-600 shadow-sm">
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-100">
                                        {{ $department->department_name }}
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium uppercase text-gray-600 dark:text-gray-300">
                                        {{ $department->department_code ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border shadow-sm
                                            {{ $department->is_active
                                                ? 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500'
                                                : 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600' }}">
                                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border shadow-sm
                                            {{ $department->is_available
                                                ? 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500'
                                                : 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600' }}">
                                            {{ $department->is_available ? 'Yes' : 'No' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium text-center">
                                        @php
                                            [$active, $total] = explode(' / ', $department->hrLiaisonsStatus);
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border shadow-sm
                                            {{ $active > 0 ? 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500' : 'bg-red-100 text-red-800 border-red-400 dark:bg-red-900/40 dark:text-red-300 dark:border-red-500' }}">
                                            {{ $active }} online / {{ $total }} total
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center space-x-1" x-data="{
                                        open: false,
                                        openAdd: false,
                                        openRemove: false,
                                        openEdit: false,
                                        openDelete: false,
                                        openCreate: false,
                                        openCreateLiaison: false
                                    }"
                                    @close-all-modals.window="
                                        open = false;
                                        openAdd = false;
                                        openRemove = false;
                                        openEdit = false;
                                        openDelete = false;
                                        openCreate = false;
                                        openCreateLiaison = false;
                                    " >
                                        <div>
                                            <button @click="open = !open"
                                                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                                                <x-heroicon-o-ellipsis-horizontal class="w-6 h-6 text-black dark:text-white"/>
                                            </button>

                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 z-50 overflow-hidden">

                                                <div class="flex flex-col divide-y divide-gray-200 dark:divide-zinc-700">

                                                    <a
                                                        href="{{ route('admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view', $department->department_id) }}"
                                                        wire:navigate
                                                        class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm font-medium">
                                                        <x-heroicon-o-eye class="w-4 h-4 text-blue-500"/>
                                                        View
                                                    </a>

                                                    <button
                                                        @click="open = false; openEdit = true; $wire.resetFields(); $wire.editDepartment({{ $department->department_id }})"
                                                        class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm font-medium">
                                                        <x-heroicon-o-pencil class="w-4 h-4 text-green-500"/>
                                                        Edit
                                                    </button>

                                                    <button
                                                        @click="open = false; openAdd = true; $wire.resetFields(); $wire.loadAvailableLiaisons({{ $department->department_id }})"
                                                        class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm font-medium text-green-600">
                                                        <x-heroicon-o-user-plus class="w-4 h-4" />
                                                        Add HR Liaison
                                                    </button>

                                                    <button
                                                        @click="open = false; openRemove = true; $wire.resetFields(); $wire.loadRemoveLiaisons({{ $department->department_id }})"
                                                        class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm font-medium text-amber-600">
                                                        <x-heroicon-o-user-minus class="w-4 h-4" />
                                                        Remove HR Liaison
                                                    </button>

                                                    <button
                                                        @click="open = false; openDelete = true"
                                                        wire:click="resetFields"
                                                        class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm font-medium text-red-600">
                                                        <x-heroicon-o-trash class="w-4 h-4"/>
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="openAdd" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">
                                            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6">
                                                <h3 class="text-lg font-semibold border-b border-gray-300 dark:border-zinc-700 mb-3 text-gray-700 dark:text-gray-200">Add HR Liaison</h3>

                                                <div wire:loading.remove wire:target="loadAvailableLiaisons({{ $department->department_id }})">
                                                    <div wire:key="save-liaisons-{{ $department->department_id }}">

                                                        <x-multiple-select
                                                            name="selectedLiaisonsToAdd"
                                                            :options="$availableLiaisons"
                                                            placeholder="Select HR Liaisons"
                                                        />

                                                        <flux:error name="selectedLiaisonsToAdd" />
                                                    </div>

                                                    <div class="mt-5 flex justify-end gap-2 border-t border-gray-300 dark:border-zinc-700">
                                                        <button
                                                            @click="openAdd = false; $wire.resetFields();"
                                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                                                dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60"
                                                        >
                                                            Cancel
                                                        </button>
                                                        <button
                                                            wire:click="saveLiaison({{ $department->department_id }})"
                                                            class="px-3 py-1 text-xs rounded-md border border-green-400 text-green-700 bg-green-50 hover:bg-green-100
                                                                dark:bg-green-900/40 dark:text-green-300 dark:border-green-600 dark:hover:bg-green-900/60
                                                                disabled:opacity-50 disabled:cursor-not-allowed"
                                                            wire:loading.attr="disabled"
                                                            wire:target="saveLiaison"
                                                        >
                                                            <span wire:loading.remove wire:target="saveLiaison">
                                                                <x-heroicon-o-user-plus class="w-4 h-4 inline-block mr-1" /> Add
                                                            </span>
                                                            <span wire:loading wire:target="saveLiaison">
                                                                Processing...
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div wire:loading wire:target="loadAvailableLiaisons({{ $department->department_id }})" >
                                                    <div class="flex items-center justify-center gap-2 w-full">
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div x-show="openRemove" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">
                                            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6">
                                                <h3 class="text-lg font-semibold mb-3 border-b border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-gray-200">Remove HR Liaison</h3>

                                                <div wire:loading.remove wire:target="loadRemoveLiaisons({{ $department->department_id }})">
                                                    <div wire:key="remove-liaisons-{{ $department->department_id }}">
                                                        <x-multiple-select
                                                            name="selectedLiaisonsToRemove"
                                                            :options="$removeLiaisons"
                                                            placeholder="Select HR Liaisons to Remove"
                                                        />

                                                        <flux:error name="selectedLiaisonsToRemove" />
                                                    </div>

                                                    <div class="mt-5 flex justify-end gap-2 border-t border-gray-300 dark:border-zinc-700">

                                                        <button
                                                            @click="openRemove = false; $wire.resetFields();"
                                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                                                dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60"
                                                        >
                                                            Cancel
                                                        </button>

                                                        <button
                                                            wire:click="removeLiaison({{ $department->department_id }})"
                                                            class="px-3 py-1 text-xs rounded-md border border-red-400 text-red-700 bg-red-50 hover:bg-red-100
                                                                dark:bg-red-900/40 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/60
                                                                disabled:opacity-50 disabled:cursor-not-allowed"
                                                            wire:loading.attr="disabled"
                                                            wire:target="removeLiaison"
                                                        >
                                                            <span wire:loading.remove wire:target="removeLiaison">
                                                                <x-heroicon-o-user-minus class="w-4 h-4 inline-block mr-1" /> Remove
                                                            </span>
                                                            <span wire:loading wire:target="removeLiaison">
                                                                Processing...
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div wire:loading wire:target="loadRemoveLiaisons({{ $department->department_id }})" >
                                                    <div class="flex items-center justify-center gap-2 w-full">
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div x-show="openDelete" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">
                                            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6"
                                                 wire:key="delete-department-{{ $department->department_id }}"
                                                >
                                                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 mx-auto">
                                                    <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                                                </div>

                                                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Confirm Deletion</h2>
                                                <p class="text-sm text-gray-600 dark:text-gray-300">Are you sure you want to delete this department? This action cannot be undone.</p>

                                                <div wire:loading.remove wire:target="deleteDepartment({{ $department->department_id }})" class="flex justify-center gap-3 mt-4">
                                                    <button type="button" @click="openDelete = false; $wire.resetFields();"
                                                        class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <flux:button variant="danger" icon="trash" wire:click="deleteDepartment({{ $department->department_id }})">
                                                        Yes, Delete
                                                    </flux:button>
                                                </div>

                                                <div wire:loading wire:target="deleteDepartment({{ $department->department_id }})" >
                                                    <div class="flex items-center justify-center gap-2 w-full">
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="openEdit" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">

                                            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md
                                                        max-h-[90vh] flex flex-col overflow-y-auto">

                                                <header class="flex gap-2 items-center justify-start border-b border-gray-300 dark:border-zinc-700 sticky top-0 bg-white dark:bg-zinc-800 z-10 p-3">
                                                    <x-heroicon-o-pencil-square class="w-6 h-6" />
                                                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 m-0">
                                                        Edit Department
                                                    </h3>
                                                </header>

                                                <div wire:loading wire:target="editDepartment({{ $department->department_id }})">
                                                    <div class="w-full flex items-center justify-center gap-2 py-6">
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                                    </div>
                                                </div>

                                                <div wire:loading.remove wire:target="editDepartment({{ $department->department_id }})">
                                                    <div class="flex-1 overflow-y-auto p-6 space-y-3">
                                                        <flux:input type="text" wire:model.defer="editingDepartment.department_name" placeholder="Department Name" clearable/>
                                                        <flux:error name="editingDepartment.department_name" />

                                                        <flux:input type="text" wire:model.defer="editingDepartment.department_code" placeholder="Department Code" clearable/>
                                                        <flux:error name="editingDepartment.department_code" />

                                                        <flux:textarea wire:model.defer="editingDepartment.department_description" placeholder="Department Description" clearable/>
                                                        <flux:error name="editingDepartment.department_description" />

                                                        <x-select
                                                            name="editingDepartment.is_active"
                                                            placeholder="Select active status"
                                                            :options="['Inactive','Active']"
                                                        />
                                                        <flux:error name="editingDepartment.is_active" />

                                                        <x-select
                                                            name="editingDepartment.is_available"
                                                            placeholder="Select availability status"
                                                            :options="['Yes','No']"
                                                        />
                                                        <flux:error name="editingDepartment.is_available" />

                                                        <div class="flex flex-col items-center justify-center space-y-4 mt-4">
                                                            @php
                                                                $palette = ['0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899','14B8A6','6366F1','F97316','84CC16'];
                                                                $index = crc32($department->department_name) % count($palette);
                                                                $bgColor = $palette[$index];
                                                            @endphp

                                                            <!-- Profile Upload -->
                                                            <div
                                                                x-data="{ uploading: false, progress: 0 }"
                                                                x-init="
                                                                    $el.addEventListener('livewire-upload-start', () => uploading = true);
                                                                    $el.addEventListener('livewire-upload-progress', (event) => { progress = event.detail.progress });
                                                                    $el.addEventListener('livewire-upload-finish', () => uploading = false);
                                                                    $el.addEventListener('livewire-upload-error', () => uploading = false);
                                                                "
                                                                class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-gray-300 dark:border-zinc-700"
                                                            >
                                                                <input type="file" id="edit_profile_input" wire:model="edit_department_profile" accept=".jpg,.jpeg,.png" class="hidden">
                                                                <label for="edit_profile_input" class="cursor-pointer w-full h-full block">
                                                                    <img
                                                                        src="{{ $edit_department_profile ? $edit_department_profile->temporaryUrl() : ($profilePreview ?? 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) . '&background=' . $bgColor . '&color=fff&size=256') }}"
                                                                        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                                                        alt="Profile Preview"
                                                                    >
                                                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                                                                        <div class="text-white flex flex-col items-center gap-1">
                                                                            <x-heroicon-o-camera class="w-6 h-6"/>
                                                                            <span class="text-sm font-medium">Change</span>
                                                                        </div>
                                                                    </div>
                                                                </label>

                                                                <!-- Progress Bar -->
                                                                <div x-show="uploading" class="absolute bottom-0 left-0 w-full mt-1">
                                                                    <div class="relative w-full bg-gray-200 dark:bg-zinc-700 h-2 rounded overflow-hidden">
                                                                        <div class="absolute left-0 top-0 h-2 bg-blue-500 transition-all" :style="'width: ' + progress + '%'"></div>
                                                                    </div>
                                                                    <span class="text-xs text-gray-700 dark:text-gray-300 mt-1" x-text="progress + '%'"></span>
                                                                </div>
                                                            </div>

                                                            <!-- Background Upload -->
                                                            <div
                                                                x-data="{ uploading: false, progress: 0 }"
                                                                x-init="
                                                                    $el.addEventListener('livewire-upload-start', () => uploading = true);
                                                                    $el.addEventListener('livewire-upload-progress', (event) => { progress = event.detail.progress });
                                                                    $el.addEventListener('livewire-upload-finish', () => uploading = false);
                                                                    $el.addEventListener('livewire-upload-error', () => uploading = false);
                                                                "
                                                                class="relative w-full max-w-md h-40 rounded overflow-hidden border-4 border-gray-300 dark:border-zinc-700 mt-4"
                                                            >
                                                                <input type="file" id="edit_background_input" wire:model="edit_department_background" accept=".jpg,.jpeg,.png" class="hidden">
                                                                <label for="edit_background_input" class="cursor-pointer w-full h-full block">
                                                                    <img
                                                                        src="{{ $edit_department_background ? $edit_department_background->temporaryUrl() : ($backgroundPreview ?? 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) . '&background=' . $bgColor . '&color=fff&size=512') }}"
                                                                        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                                                        alt="Background Preview"
                                                                    >
                                                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                                                                        <div class="text-white flex flex-col items-center gap-1">
                                                                            <x-heroicon-o-camera class="w-6 h-6"/>
                                                                            <span class="text-sm font-medium">Change</span>
                                                                        </div>
                                                                    </div>
                                                                </label>

                                                                <!-- Progress Bar -->
                                                                <div x-show="uploading" class="absolute bottom-0 left-0 w-full mt-1">
                                                                    <div class="relative w-full bg-gray-200 dark:bg-zinc-700 h-2 rounded overflow-hidden">
                                                                        <div class="absolute left-0 top-0 h-2 bg-green-500 transition-all" :style="'width: ' + progress + '%'"></div>
                                                                    </div>
                                                                    <span class="text-xs text-gray-700 dark:text-gray-300 mt-1" x-text="progress + '%'"></span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <footer class="flex justify-end gap-2 border-t border-gray-300 dark:border-zinc-700 p-3 bg-white dark:bg-zinc-800 sticky bottom-0 z-10 shadow-inner">
                                                        <button @click="openEdit = false; $wire.resetFields();"
                                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                                                dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60">
                                                            Cancel
                                                        </button>

                                                        <button
                                                            wire:click="updateDepartment({{ $department->department_id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="updateDepartment({{ $department->department_id }})"
                                                            class="px-3 py-1 text-xs rounded-md border border-blue-400 text-white bg-blue-600 hover:bg-blue-700
                                                                dark:bg-blue-900 dark:text-blue-300 dark:border-blue-600 dark:hover:bg-blue-800
                                                                disabled:opacity-20 disabled:cursor-not-allowed">
                                                            <span wire:loading.remove wire:target="updateDepartment({{ $department->department_id }})">Save</span>
                                                            <span wire:loading wire:target="updateDepartment({{ $department->department_id }})">Processing...</span>
                                                        </button>
                                                    </footer>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-building-office-2 class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                                        No departments found
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>

                        </table>
                    </div>
                </div>

                <div wire:loading wire:target="previousPage, nextPage, gotoPage, applySearch, clearSearch"
                    class="overflow-x-auto w-full rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 animate-pulse">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="bg-gray-100 dark:bg-zinc-900">
                            <tr>
                                @for ($i = 0; $i < 7; $i++)
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <div class="h-3 bg-gray-300 dark:bg-zinc-700 rounded w-3/4"></div>
                                    </th>
                                @endfor
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                            @for ($row = 0; $row < 5; $row++)
                                <tr>
                                    @for ($col = 0; $col < 6; $col++)
                                        <td class="px-4 py-3 align-middle">
                                            @if($col === 0)
                                                <div class="h-4 w-4 rounded bg-gray-200 dark:bg-zinc-700"></div>
                                            @else
                                                <div class="h-3 bg-gray-200 dark:bg-zinc-700 rounded w-full"></div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4">
                    {{ $departments->links() }}
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
                    Create HR Liaison
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
    </div>




