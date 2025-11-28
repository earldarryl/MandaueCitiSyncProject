<div class="p-6 space-y-6 relative w-full"
     data-component="admin-citizens-index"
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
            class="relative w-full flex flex-col"
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
                class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 px-6"
            >
                <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                    <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                        <x-heroicon-o-user-group class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                    </div>
                    <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Total Citizens</p>
                    <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $totalCitizens }}</p>
                </div>

                <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                    <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                        <x-heroicon-o-user class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                    </div>
                    <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Male Citizens</p>
                    <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $totalMale }}</p>
                </div>

                <div class="group relative bg-gradient-to-br from-pink-50 to-pink-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-pink-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-pink-200/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                    <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-pink-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                        <x-heroicon-o-user class="h-8 w-8 text-pink-600 dark:text-pink-400" />
                    </div>
                    <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Female Citizens</p>
                    <p class="relative text-3xl font-bold text-pink-600 dark:text-pink-400 tracking-tight">{{ $totalFemale }}</p>
                </div>

                <div class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-200/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                    <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                        <x-heroicon-o-signal class="h-8 w-8 text-green-600 dark:text-green-400" />
                    </div>
                    <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Active (Online)</p>
                    <p class="relative text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">{{ $totalOnline }}</p>
                </div>
            </div>
        </div>

        <div class="w-full h-full p-6">

            <div
                x-data="{ selectedColumn: @entangle('filterColumnInput') }"
                class="flex flex-col md:flex-row md:items-center md:justify-center gap-3 mb-4 w-full px-4"
            >
                <div class="w-full md:w-1/2">
                    <x-filter-select
                        name="filterColumnInput"
                        placeholder="Select Column to Filter"
                        :options="[
                            'First Name',
                            'Middle Name',
                            'Last Name',
                            'Email',
                            'Barangay'
                        ]"
                    />
                </div>

                <div
                    x-show="selectedColumn"
                    x-transition
                    class="w-full md:w-1/2"
                >
                    <x-filter-select
                        name="nameStartsWithInput"
                        placeholder="Filter by Letter"
                        :options="[
                            'A','B','C','D','E','F','G','H','I','J','K','L','M',
                            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
                        ]"
                    />
                </div>

                <div class="w-full md:w-1/3">
                    <x-filter-select
                        name="genderFilterInput"
                        placeholder="Filter by Gender"
                        :options="[
                            'Male',
                            'Female'
                        ]"
                    />
                </div>

                <div class="w-full md:w-1/4">
                    <x-filter-select
                        name="statusFilterInput"
                        placeholder="Filter by Status"
                        :options="['Online', 'Away', 'Offline']"
                        wire:model="statusFilterInput"
                    />
                </div>

                <div class="w-full md:w-1/4">
                    <x-filter-select
                        name="deactivatedFilterInput"
                        placeholder="Filter by Account"
                        :options="['Active', 'Deactivated']"
                        wire:model="deactivatedFilterInput"
                    />
                </div>

            </div>

            <div class="flex w-full flex-1 px-3 mb-3">
                <button
                    wire:click="applyFilters"
                    class="flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white w-full font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300"
                >
                    <flux:icon.adjustments-horizontal class="w-4 h-4" />
                    <span>Apply Filters</span>
                </button>
            </div>

            <div class="flex w-full flex-1 px-3 mb-3">
                <div class="relative w-full font-bold">
                    <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>

                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>

                        <input
                            type="text"
                            id="search"
                            wire:model.defer="searchInput"
                            wire:keydown.enter="applySearch"
                            placeholder="Search citizens..."
                            class="block w-full p-4 ps-10 pe-28 text-sm text-gray-900 border border-gray-300 rounded-lg
                                bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                dark:bg-zinc-800 dark:border-gray-600 dark:placeholder-gray-400
                                dark:text-white dark:focus:outline-none dark:focus:ring-2 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                        />

                        <button
                            type="button"
                            wire:click="clearSearch"
                            class="absolute inset-y-0 right-28 flex items-center justify-center text-gray-500
                                hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors"
                        >
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>

                        <button
                            type="button"
                            wire:click="applySearch"
                            class="absolute inset-y-0 right-0 my-auto inline-flex items-center justify-center gap-2
                                px-4 py-2 text-sm font-semibold rounded-r-xl
                                text-white bg-gradient-to-r from-blue-600 to-blue-700
                                hover:from-blue-700 hover:to-blue-800
                                focus:outline-none focus:ring-0
                                shadow-sm hover:shadow-md transition-all duration-200"
                        >
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                            <span>Search</span>
                        </button>
                    </div>
                </div>
            </div>

            <div wire:poll.15s wire:loading.remove wire:target="previousPage, nextPage, gotoPage, applySearch, clearSearch, applyFilters">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th wire:click="sortBy('first_name')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>First Name</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'first_name')
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
                                <th wire:click="sortBy('middle_name')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Middle Name</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'middle_name')
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
                                <th wire:click="sortBy('last_name')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Last Name</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'last_name')
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
                                <th wire:click="sortBy('name')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Username</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'name')
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
                                <th wire:click="sortBy('email')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Email</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'email')
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

                                <th class="px-6 py-3 text-left">Barangay</th>
                                <th class="px-6 py-3 text-left">Gender</th>
                                <th class="px-6 py-3 text-left">Phone</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-center">Is Deactivated</th>

                                <th wire:click="sortBy('created_at')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Registered</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'created_at')
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
                            @forelse($citizens as $citizen)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-100">
                                        {{ $citizen->userInfo?->first_name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-100">
                                        {{ $citizen->userInfo?->middle_name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-100">
                                        {{ $citizen->userInfo?->last_name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $citizen->name }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $citizen->email }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        {{ $citizen->userInfo?->barangay ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        @php
                                            $gender = strtolower($citizen->userInfo?->gender ?? 'Unknown');
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border shadow-sm
                                            {{ $gender === 'male'
                                                ? 'bg-blue-100 text-blue-800 border-blue-400 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-500'
                                                : ($gender === 'female'
                                                    ? 'bg-pink-100 text-pink-800 border-pink-400 dark:bg-pink-900/40 dark:text-pink-300 dark:border-pink-500'
                                                    : 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600') }}">
                                            {{ ucfirst($citizen->userInfo?->gender ?? '—') }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-xs font-medium">
                                        {{ $citizen->userInfo?->phone_number ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border shadow-sm
                                            {{ $citizen->status === 'online'
                                                ? 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500'
                                                : ($citizen->status === 'away'
                                                    ? 'bg-yellow-100 text-yellow-800 border-yellow-400 dark:bg-yellow-900/40 dark:text-yellow-300 dark:border-yellow-500'
                                                    : 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600') }}">
                                            {{ ucfirst($citizen->status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-center">
                                        @if($citizen->is_deactivated)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-800 border border-gray-400 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                                                Deactivated
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500">
                                                Active
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-300 font-medium">
                                        {{ $citizen->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-1">
                                        <a href="{{ route('admin.stakeholders.citizens.view', $citizen->userInfo?->id) }}" wire:navigate class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 dark:bg-zinc-700 dark:text-gray-200">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-archive-box-x-mark class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                                        No citizen users found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div wire:loading wire:target="previousPage, nextPage, gotoPage, applySearch, clearSearch, applyFilters"
                class="overflow-x-auto w-full rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 animate-pulse">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-100 dark:bg-zinc-900">
                        <tr>
                            @for ($i = 0; $i < 7; $i++)
                                <th class="px-4 py-2">
                                    <div class="h-3 bg-gray-300 dark:bg-zinc-700 rounded w-3/4"></div>
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @for ($row = 0; $row < 5; $row++)
                            <tr>
                                @for ($col = 0; $col < 7; $col++)
                                    <td class="px-4 py-3">
                                        <div class="h-3 bg-gray-200 dark:bg-zinc-700 rounded w-full"></div>
                                    </td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $citizens->links() }}
            </div>
        </div>
    </div>
</div>
