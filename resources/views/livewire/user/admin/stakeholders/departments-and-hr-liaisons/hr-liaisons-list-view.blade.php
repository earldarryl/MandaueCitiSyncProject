<div class="relative w-full"
     data-component="admin-hr-liaisons-list-view"
     data-wire-id="{{ $this->id() }}"
>
    <div class="flex flex-col gap-6">
       <div class="flex-1">
            @php
                $palette = ['0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899','14B8A6','6366F1','F97316','84CC16'];
                $index = crc32($department->department_name) % count($palette);
                $bgColor = $palette[$index];
            @endphp
            <img
                src="{{ $department->department_bg
                    ? Storage::url($department->department_bg)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) .
                    '&background=' . $bgColor . '&color=fff&size=512' }}"
                alt="Department BG"
                class="w-full h-52 object-cover rounded-b-xl mb-4"
            >

            <a href="{{ route('admin.stakeholders.departments-and-hr-liaisons.index') }}" wire:navigate
            class="absolute top-4 left-4 flex items-center gap-2 px-3 py-2 bg-gray-600/20 text-white text-sm font-semibold rounded-lg shadow hover:bg-gray-700/50 transition-all duration-200">
                <flux:icon.arrow-long-left class="w-4 h-4" />
            </a>

            <div class="flex items-center gap-4 px-4">
                @php
                    $indexProfile = crc32($department->department_name) % count($palette);
                    $profileBg = $palette[$indexProfile];
                @endphp
                <img
                    src="{{ $department->department_profile
                        ? Storage::url($department->department_profile)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) .
                        '&background=' . $profileBg . '&color=fff&size=128' }}"
                    alt="Profile"
                    class="w-16 h-16 rounded-full border border-gray-200 dark:border-zinc-700"
                >
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $department->department_name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $department->department_description }}</p>
                </div>
            </div>
        </div>

        <div class="m-6 overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800">
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

                            <td class="px-6 py-4 text-sm font-medium text-gray-600 dark:text-gray-300">
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

                            <td class="px-6 py-4 text-center space-x-1" x-data="{ openRemove: false, openEditLiaison: false }">
                                <button
                                    @click="openRemove = true"
                                    class="px-3 py-1 text-xs rounded-md border border-red-400 text-red-700 bg-red-50 hover:bg-red-100
                                    dark:bg-red-900/40 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/60">
                                    <span wire:loading.remove wire:target="removeLiaison({{ $liaison->id }})">
                                        <x-heroicon-o-user-minus class="w-4 h-4 inline-block mr-1" /> Remove
                                    </span>
                                    <span wire:loading wire:target="removeLiaison({{ $liaison->id }})">Processing...</span>
                                </button>

                                <button
                                    @click="openEditLiaison = true"
                                    wire:click="editHrLiaisonModal({{ $liaison->id }})"
                                    class="px-3 py-1 text-xs rounded-md border border-blue-400 text-blue-700 bg-blue-50 hover:bg-blue-100
                                    dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-600 dark:hover:bg-blue-900/60">
                                    <span wire:loading.remove wire:target="updateHrLiaison({{ $liaison->id }})">
                                        <x-heroicon-o-pencil class="w-4 h-4 inline-block mr-1" /> Edit
                                    </span>
                                    <span wire:loading wire:target="updateHrLiaison({{ $liaison->id }})">Processing...</span>
                                </button>

                                <div
                                    x-show="openEditLiaison"
                                    x-transition
                                    class="fixed inset-0 flex items-center justify-center z-50 bg-black/20"
                                    @click.self="openEditLiaison = false"
                                >
                                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md max-h-[90vh] overflow-y-auto flex flex-col">

                                        <header class="flex gap-2 items-center justify-start border border-gray-300 dark:border-zinc-800 sticky top-0 bg-white dark:bg-zinc-800 z-10 p-3">
                                            <x-heroicon-o-user-circle class="w-6 h-6" />
                                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 m-0">
                                                Edit HR Liaison
                                            </h3>
                                        </header>

                                        <div wire:target="editHrLiaisonModal({{ $liaison->id }})" wire:loading>
                                            <div class="w-full flex items-center justify-center gap-2 py-6">
                                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                            </div>
                                        </div>

                                        <div wire:target="editHrLiaisonModal({{ $liaison->id }})" wire:loading.remove>
                                            <div class="space-y-3 w-full p-6">
                                                <flux:input type="text" wire:model.defer="editLiaison.name" placeholder="Full Name" clearable/>
                                                <flux:error name="editLiaison.name" />

                                                <flux:input type="email" wire:model.defer="editLiaison.email" placeholder="Email Address" clearable/>
                                                <flux:error name="editLiaison.email" />

                                                <flux:input type="password" wire:model.defer="editLiaison.password" placeholder="New Password (Optional)" viewable clearable/>
                                                <flux:error name="editLiaison.password" />
                                            </div>
                                        </div>

                                        <footer class="flex justify-end gap-2 border-t border-gray-300 dark:border-zinc-700 p-3 bg-white dark:bg-zinc-800 sticky bottom-0 z-10 shadow-inner">
                                            <button @click="openEditLiaison = false"
                                                    class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                                        dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60">
                                                Cancel
                                            </button>
                                            <button wire:click="updateHrLiaison({{ $liaison->id }})" wire:loading.attr="disabled" @click="openEditLiaison = false"
                                                    class="px-3 py-1 text-xs rounded-md border border-blue-400 text-white bg-blue-600 hover:bg-blue-700
                                                        dark:bg-blue-900 dark:text-blue-300 dark:border-blue-600 dark:hover:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <span wire:loading.remove wire:target="updateHrLiaison({{ $liaison->id }})">Update</span>
                                                <span wire:loading wire:target="updateHrLiaison({{ $liaison->id }})">Processing...</span>
                                            </button>
                                        </footer>
                                    </div>
                                </div>

                                <div x-show="openRemove" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/20" @click.self="openRemove = false">
                                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6">
                                        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 mx-auto">
                                            <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                                        </div>

                                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Confirm Removal</h2>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Are you sure you want to remove this liaison? This action cannot be undone.</p>

                                        <div wire:loading.remove wire:target="removeLiaison({{ $liaison->id }})" class="flex justify-center gap-3 mt-4">
                                            <button type="button" @click="openRemove = false"
                                                class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                                                Cancel
                                            </button>
                                            <flux:button variant="danger" icon="trash" wire:click="removeLiaison({{ $liaison->id }})" @click="openRemove = false">
                                                Yes, Delete
                                            </flux:button>
                                        </div>

                                        <div wire:loading wire:target="removeLiaison({{ $liaison->id }})" >
                                            <div class="flex items-center justify-center gap-2 w-full">
                                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
