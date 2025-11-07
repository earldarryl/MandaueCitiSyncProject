<div class="p-6 space-y-6 relative w-full" x-data="{ openAdd: false, openRemove: false }">

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

                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
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

                        <td class="px-6 py-4 text-sm text-center text-gray-700 dark:text-gray-300">
                            {{ $department->hr_liaisons_count ?? 0 }}
                        </td>

                        <td class="px-6 py-4 text-center space-x-1" x-data="{ openAdd: false, openRemove: false }">
                            <a href="{{ route('admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view', $department->department_id) }}" wire:navigate class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 dark:bg-zinc-700 dark:text-gray-200">View</a>

                            <button
                                @click="openAdd = true"
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

                            <button
                                @click="openRemove = true"
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
</div>
