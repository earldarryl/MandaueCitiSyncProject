<div class="p-6 space-y-6 relative w-full">
    <div class="flex w-full">
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

    <div wire:poll.15s wire:loading.remove wire:target="previousPage, nextPage, gotoPage, applySearch, clearSearch, applyFilters">
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
                                <span>Status</span>
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
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                        <td class="px-6 py-4">
                            <img src="{{ $department->department_profile_url ?? asset('images/default-dept.png') }}"
                                class="w-10 h-10 rounded-full object-cover border border-gray-300 dark:border-zinc-600 shadow-sm">
                        </td>

                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ $department->department_name }}
                        </td>

                        <td class="px-6 py-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                            {{ $department->department_code ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border shadow-sm
                                {{ $department->is_active
                                    ? 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500'
                                    : 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600' }}">
                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm font-medium text-center text-gray-700 dark:text-gray-300">
                            {{ $department->hr_liaisons_count ?? 0 }}
                        </td>

                        <td class="px-6 py-4 text-center space-x-1" x-data="{ openAdd: false, openRemove: false, openEdit: false }">
                            <a href="{{ route('admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view', $department->department_id) }}" wire:navigate class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 dark:bg-zinc-700 dark:text-gray-200">View</a>

                            <button
                                wire:loading.attr="disabled"
                                wire:target="updateDepartment({{ $department->department_id }})"
                                @click="openEdit = true; $wire.editDepartment({{ $department->department_id }})"
                                class="px-3 py-1 text-xs rounded-md border border-blue-400 text-blue-700 bg-blue-50 hover:bg-blue-100
                                    dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-600 dark:hover:bg-blue-900/60"
                            >
                                <span wire:loading.remove wire:target="updateDepartment({{ $department->department_id }})">
                                    <x-heroicon-o-pencil-square class="w-4 h-4 inline-block mr-1" /> Edit
                                </span>
                                <span wire:loading wire:target="updateDepartment({{ $department->department_id }})">
                                    Processing...
                                </span>
                            </button>

                            <button
                                @click="openAdd = true"
                                class="px-3 py-1 text-xs rounded-md border border-green-400 text-green-700 bg-green-50 hover:bg-green-100
                                    dark:bg-green-900/40 dark:text-green-300 dark:border-green-600 dark:hover:bg-green-900/60"
                                wire:loading.attr="disabled"
                                wire:target="saveLiaison({{ $department->department_id }})"
                            >
                                <span wire:loading.remove wire:target="saveLiaison({{ $department->department_id }})">
                                    <x-heroicon-o-user-plus class="w-4 h-4 inline-block mr-1" /> Add
                                </span>
                                <span wire:loading wire:target="saveLiaison({{ $department->department_id }})">
                                    Processing...
                                </span>
                            </button>

                            <button
                                @click="openRemove = true"
                                class="px-3 py-1 text-xs rounded-md border border-red-400 text-red-700 bg-red-50 hover:bg-red-100
                                    dark:bg-red-900/40 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/60"
                                wire:loading.attr="disabled"
                                wire:target="removeLiaison({{ $department->department_id }})"
                            >
                                <span wire:loading.remove wire:target="removeLiaison({{ $department->department_id }})">
                                    <x-heroicon-o-user-minus class="w-4 h-4 inline-block mr-1" /> Remove
                                </span>
                                <span wire:loading wire:target="removeLiaison({{ $department->department_id }})">
                                    Processing...
                                </span>
                            </button>

                            <div x-show="openAdd" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50 backdrop-blur-sm" @click.self="openAdd = false">
                                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-200">Add HR Liaison</h3>

                                    <x-multiple-select
                                        name="selectedLiaisonsToAdd"
                                        :options="$available = \App\Models\User::role('hr_liaison')
                                            ->whereDoesntHave('departments', function ($query) use ($department) {
                                                $query->where('hr_liaison_departments.department_id', $department->department_id);
                                            })
                                            ->pluck('name','id')
                                            ->toArray()"
                                        placeholder="Select HR Liaisons"
                                    />

                                    <div class="mt-5 flex justify-end gap-2">
                                        <button
                                            @click="openAdd = false"
                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                                dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            wire:click="saveLiaison({{ $department->department_id }})"
                                            @click="openAdd = false"
                                            class="px-3 py-1 text-xs rounded-md border border-green-400 text-green-700 bg-green-50 hover:bg-green-100
                                                dark:bg-green-900/40 dark:text-green-300 dark:border-green-600 dark:hover:bg-green-900/60"
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
                            </div>

                            <div x-show="openRemove" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50 backdrop-blur-sm" @click.self="openRemove = false">
                                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-200">Remove HR Liaison</h3>

                                    <x-multiple-select
                                        name="selectedLiaisonsToRemove"
                                        :options="$department->hrLiaisons
                                            ->pluck('name','id')
                                            ->toArray()"
                                        placeholder="Select HR Liaisons to Remove"
                                    />

                                    <div class="mt-5 flex justify-end gap-2">

                                        <button
                                            @click="openRemove = false"
                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                                dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60"
                                        >
                                            Cancel
                                        </button>

                                        <button
                                            wire:click="removeLiaison({{ $department->department_id }})"
                                            @click="openRemove = false"
                                            class="px-3 py-1 text-xs rounded-md border border-red-400 text-red-700 bg-red-50 hover:bg-red-100
                                                dark:bg-red-900/40 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/60"
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
                            </div>

                            <div x-show="openEdit" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/50 backdrop-blur-sm" @click.self="openEdit = false">
                                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-200">Edit Department</h3>

                                    <div class="space-y-3">
                                        <flux:input type="text" wire:model.defer="editingDepartment.department_name" placeholder="Department Name"/>
                                        <flux:error name="editingDepartment.department_name" />

                                        <flux:input type="text" wire:model.defer="editingDepartment.department_code" placeholder="Department Code"/>
                                        <flux:error name="editingDepartment.department_code" />

                                        <flux:textarea wire:model.defer="editingDepartment.department_description" placeholder="Department Description"/>
                                        <flux:error name="editingDepartment.department_description" />

                                        <x-select
                                            name="editingDepartment.is_active"
                                            placeholder="Select status"
                                            :options="['Inactive','Active']"
                                            />
                                        <flux:error name="is_active" />

                                    </div>

                                    <div class="mt-5 flex justify-end gap-2">
                                        <button @click="openEdit = false"
                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                                dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60">
                                            Cancel
                                        </button>
                                        <button wire:click="updateDepartment({{ $department->department_id }})" @click="openEdit = false"
                                            class="px-3 py-1 text-xs rounded-md border border-blue-400 text-blue-700 bg-blue-50 hover:bg-blue-100
                                                dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-600 dark:hover:bg-blue-900/60">
                                            Save
                                        </button>
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

    <div wire:loading wire:target="previousPage, nextPage, gotoPage, applySearch, clearSearch, applyFilters"
        class="overflow-x-auto w-full rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 animate-pulse">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="bg-gray-100 dark:bg-zinc-900">
                <tr>
                    @for ($i = 0; $i < 6; $i++)
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
</div>
