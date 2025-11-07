<div class="p-6 space-y-6 relative w-full">
    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto py-2">

        <x-responsive-nav-link
            href="{{ route('admin.stakeholders.departments-and-hr-liaisons.index') }}"
            wire:navigate
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-gray-100 dark:bg-zinc-800 text-gray-800 dark:text-gray-200
                border border-gray-500 dark:border-gray-200
                hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 w-full sm:w-58"
        >
            <x-heroicon-o-user-group class="w-5 h-5 text-gray-700 dark:text-gray-300" />
            <span class="hidden lg:inline">Return to Departments</span>
            <span class="lg:hidden">Departments</span>
        </x-responsive-nav-link>

    </div>
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Profile</th>

                    <th wire:click="sortBy('name')" class="px-6 py-3 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <span>Name</span>
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

                    <th class="px-6 py-3 text-center font-semibold">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse($hrLiaisons as $liaison)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                        <td class="px-6 py-4">
                            @php
                                $palette = [
                                    '0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899',
                                    '14B8A6','6366F1','F97316','84CC16',
                                ];
                                $index = crc32($liaison->name) % count($palette);
                                $bgColor = $palette[$index];
                            @endphp

                            <img
                                src="{{ $liaison->profile_photo_url
                                    ? $liaison->profile_photo_url
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($liaison->name) .
                                    '&background=' . $bgColor .
                                    '&color=fff&size=128' }}"
                                alt="profile-pic"
                                class="w-10 h-10 rounded-full object-cover border border-gray-300 dark:border-zinc-600 shadow-sm"
                            >
                        </td>

                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ $liaison->name }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $liaison->email }}
                        </td>

                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border shadow-sm
                                {{ $liaison->is_active
                                    ? 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500'
                                    : 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600' }}">
                                {{ $liaison->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center space-x-1">
                            <button wire:click="removeLiaison({{ $liaison->id }})"
                                class="px-3 py-1 text-xs rounded-md border border-red-400 text-red-700 bg-red-50 hover:bg-red-100
                                dark:bg-red-900/40 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/60">
                                <span wire:loading.remove wire:target="removeLiaison({{ $liaison->id }})">
                                    <x-heroicon-o-user-minus class="w-4 h-4 inline-block mr-1" /> Remove
                                </span>
                                <span wire:loading wire:target="removeLiaison({{ $liaison->id }})">Processing...</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-user-group class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                            No HR Liaisons found for this department
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
