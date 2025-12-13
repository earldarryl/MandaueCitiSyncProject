<div class="relative w-full" x-data="{ openAssignAll: false, openUnassignAll: false }"
     @close-all-modals.window="
        openAssignAll = false;
        openUnassignAll = false;
    ">

    <div class="flex flex-col sm:flex-row w-full sm:w-auto p-2">
        <x-responsive-nav-link
            href="{{ route('admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view', ['department' => $departmentId]) }}"
            wire:navigate
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-gray-100 dark:bg-zinc-800 text-gray-800 dark:text-gray-200
                border border-gray-500 dark:border-gray-200
                hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 w-full sm:w-52"
        >
            <x-heroicon-o-arrow-left class="w-5 h-5 text-gray-700 dark:text-gray-300" />
            <span class="hidden lg:inline">Back to HR Liaisons</span>
            <span class="lg:hidden">Back</span>
        </x-responsive-nav-link>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 m-4">

        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
            border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">

            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-clipboard-document-list class="h-8 w-8 text-blue-600 dark:text-blue-400"/>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Total Assignments</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $totalAssignments }}</p>
        </div>

        <div class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
            border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">

            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-200/20 to-transparent opacity-0
                group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-check class="h-8 w-8 text-green-600 dark:text-green-400"/>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Assigned</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">{{ $assignedCount }}</p>
        </div>

        <div class="group relative bg-gradient-to-br from-red-50 to-red-100 dark:from-zinc-800 dark:to-zinc-900
            border border-red-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">

            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-200/20 to-transparent opacity-0
                group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-red-200/50
                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-x-mark class="h-8 w-8 text-red-600 dark:text-red-400"/>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Unassigned</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 tracking-tight">{{ $unassignedCount }}</p>
        </div>

    </div>

    <div class="mt-4 w-full bg-white/70 dark:bg-zinc-800/50 border border-blue-200/40 dark:border-zinc-700
        p-3 shadow-sm flex lg:flex-row flex-col items-center justify-between transition">

        <div class="flex items-center gap-3">
            <div class="relative w-10 h-10 rounded-full shrink-0 dark:bg-white overflow-visible">
                <img
                    src="{{ $hrLiaison->profile_pic
                        ? Storage::url($hrLiaison->profile_pic)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($hrLiaison->name) . '&background=3B82F6&color=fff&size=128' }}"
                    alt="profile-pic"
                    class="rounded-full w-full h-full object-cover"
                />
                <span class="absolute bottom-0 right-0 w-3 h-3 z-50 rounded-full ring-1 ring-white
                    {{ $hrLiaisonProfile['is_online'] ? 'bg-green-500 dark:bg-green-400' : 'bg-gray-400 dark:bg-gray-500' }}">
                </span>
            </div>

            <div class="flex flex-col">
                <span class="text-[15px] font-medium text-gray-700 dark:text-gray-300">
                    {{ $hrLiaison->name }}
                </span>
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                    {{ $hrLiaison->email }}
                </span>
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                    Department: {{ $hrLiaison->departments->pluck('department_name')->join(', ') }}
                </span>
            </div>
        </div>

        <div class="flex justify-end items-center gap-2">
            <span class="text-[15px] font-semibold {{ $hrLiaisonProfile['is_online'] ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                {{ $hrLiaisonProfile['is_online'] ? 'Online' : 'Offline' }}
            </span>
        </div>
    </div>

    <div class="flex w-full my-4 px-2">
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
                    placeholder="Search assignments..."
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

    <div class="flex gap-2 w-full mt-6 mb-2 mx-auto px-3">

        <x-filter-select
            name="filterStatus"
            placeholder="Status"
            :options="['Assigned', 'Unassigned']"
            wire:model="filterStatus"
        />

        <x-date-picker
            name="filterDate"
            placeholder="Pick a date"
            :model="'filterDate'"
        />

    </div>

    <div class="flex flex-col justify-center w-full px-3 mb-3">
        <button
            wire:click="applyFilters"
            class="flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white w-full font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
            <flux:icon.adjustments-horizontal class="w-4 h-4" />
            <span wire:loading.remove wire:target="applyFilters">Apply Filters</span>
            <span wire:loading wire:target="applyFilters">Processing...</span>
        </button>
    </div>

    <div class="flex lg:flex-row flex-col gap-2 lg:justify-end justify-center items-center lg:items-end pt-4 px-2 bg-gray-50 dark:bg-zinc-900">
        <button
            @click="openUnassignAll = true;"
            wire:loading.attr="disabled"
            wire:target="unassignAll"
            class="px-4 py-2 w-full lg:w-auto rounded-md border border-red-400 text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900/40 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/60"
        >
            <span wire:loading.remove wire:target="unassignAll">
                <span class="flex gap-2 justify-center items-center">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    <span>Unassign All</span>
                </span>
            </span>
            <span wire:loading wire:target="unassignAll">
                Processing...
            </span>
        </button>

        <button
            @click="openAssignAll = true;"
            wire:loading.attr="disabled"
            wire:target="assignAll"
            class="px-4 py-2 w-full lg:w-auto rounded-md border border-green-400 text-green-700 bg-green-50 hover:bg-green-100 dark:bg-green-900/40 dark:text-green-300 dark:border-green-600 dark:hover:bg-green-900/60"
        >
            <span wire:loading.remove wire:target="assignAll">
                <span class="flex gap-2 justify-center items-center">
                    <x-heroicon-o-plus class="w-4 h-4" />
                    <span>Assign All</span>
                </span>
            </span>
            <span wire:loading wire:target="assignAll">
                Processing...
            </span>
        </button>

    </div>

    <div class="relative w-full p-2 bg-gray-50 dark:bg-zinc-900">
        <div wire:loading.remove wire:target="previousPage, nextPage, gotoPage, assignUnassigned, applySearch">
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800">

                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3 text-center font-medium">Grievance Ticket</th>
                            <th class="px-6 py-3 text-left font-medium">Title</th>
                            <th class="px-6 py-3 text-center font-medium">Department</th>
                            <th class="px-6 py-3 text-center font-medium">Assigned At</th>
                            <th class="px-6 py-3 text-center font-medium">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @forelse ($assignments as $assignment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition font-medium
                                @if($assignment->hr_liaison_id !== $hrLiaison->id) bg-gray-100 dark:bg-gray-800/40 @endif">

                                <td class="px-6 py-4 text-center text-sm font-medium text-blue-600 dark:text-blue-400">
                                    {{ $assignment->grievance->grievance_ticket_id }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $assignment->grievance->grievance_title }}
                                </td>

                                <td class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">
                                    {{ ($assignment->department && $assignment->hr_liaison_id)
                                        ? $assignment->department->department_name
                                        : 'Unassigned'
                                    }}
                                </td>

                                <td class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">
                                    @if($assignment->hr_liaison_id && $assignment->assigned_at)
                                        {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('M d, Y h:i A') }}
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center"
                                    x-data="{ open: false }"
                                    @close-all-modals.window="open = false"
                                >
                                    <div class="inline-block text-left">
                                        <button @click="open = !open"
                                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                                            <x-heroicon-o-ellipsis-horizontal class="w-6 h-6 text-gray-700 dark:text-gray-200"/>
                                        </button>

                                        <div x-show="open" x-transition @click.away="open = false"
                                            class="absolute right-4 w-44 bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 z-50">
                                            <div class="flex flex-col divide-y divide-gray-200 dark:divide-zinc-700">

                                                <a href="{{ route('admin.forms.grievances.view', $assignment->grievance->grievance_ticket_id) }}"
                                                wire:navigate
                                                class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm">
                                                    <x-heroicon-o-eye class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                                    View
                                                </a>

                                                @if($assignment->hr_liaison_id)
                                                    <button
                                                        wire:click="unassignSingle({{ $assignment->grievance_id }})"
                                                        wire:loading.attr="disabled"
                                                        class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm text-red-500 w-full">
                                                        <x-heroicon-o-user-minus class="w-4 h-4"/>
                                                        <span wire:loading.remove wire:target="unassignSingle({{ $assignment->grievance_id }})">
                                                            Unassign
                                                        </span>
                                                        <span wire:loading wire:target="unassignSingle({{ $assignment->grievance_id }})">
                                                            Processing...
                                                        </span>
                                                    </button>
                                                @else
                                                    <button
                                                        wire:click="assignSingle({{ $assignment->grievance_id }})"
                                                        wire:loading.attr="disabled"
                                                        class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm text-green-500 w-full">
                                                        <x-heroicon-o-user-plus class="w-4 h-4"/>
                                                        <span wire:loading.remove wire:target="assignSingle({{ $assignment->grievance_id }})">
                                                            Assign
                                                        </span>
                                                        <span wire:loading wire:target="assignSingle({{ $assignment->grievance_id }})">
                                                            Processing...
                                                        </span>
                                                    </button>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                    <x-heroicon-o-x-circle class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                                    No assignments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

        <div wire:loading wire:target="previousPage, nextPage, gotoPage, assignUnassigned, applySearch"
            class="overflow-x-auto w-full rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 animate-pulse">

            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-900">
                    <tr>
                        @for ($i = 0; $i < 5; $i++)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <div class="h-3 bg-gray-300 dark:bg-zinc-700 rounded w-3/4"></div>
                            </th>
                        @endfor
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @for ($row = 0; $row < 10; $row++)
                        <tr>
                            @for ($col = 0; $col < 4; $col++)
                                <td class="px-6 py-4 align-middle">
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
            {{ $assignments->links() }}
        </div>

    </div>


    <div x-show="openAssignAll" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-500/20 dark:bg-blue-500/30 mx-auto">
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-blue-500 dark:text-blue-400" />
            </div>

            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mt-4 text-center">Confirm Assign All</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 text-center">
                Are you sure you want to assign all grievances to <b>{{ $hrLiaison->name }}</b>?
            </p>

            <div class="flex flex-col justify-center items-center mt-6">
                <div wire:loading.remove wire:target="assignAll" class="flex justify-center gap-3 mt-4">
                    <button type="button" @click="openAssignAll = false;"
                        class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <flux:button variant="primary" color="green" icon="check" wire:click="assignAll">
                        Yes, Assign
                    </flux:button>
                </div>

                <div wire:loading wire:target="assignAll" >
                    <div class="flex items-center justify-center gap-2 w-full">
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="openUnassignAll" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-500/20 dark:bg-blue-500/30 mx-auto">
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-blue-500 dark:text-blue-400" />
            </div>

            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mt-4 text-center">Confirm Unassign All</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 text-center">
                Are you sure you want to unassign all grievances from <b>{{ $hrLiaison->name }}</b>?
            </p>

            <div class="flex flex-col justify-center items-center mt-6">
                <div wire:loading.remove wire:target="unassignAll" class="flex justify-center gap-3 mt-4">
                    <button type="button" @click="openUnassignAll = false;"
                        class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <flux:button variant="danger" icon="check" wire:click="unassignAll">
                        Yes, Unassign
                    </flux:button>
                </div>

                <div wire:loading wire:target="unassignAll" >
                    <div class="flex items-center justify-center gap-2 w-full">
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                        <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


