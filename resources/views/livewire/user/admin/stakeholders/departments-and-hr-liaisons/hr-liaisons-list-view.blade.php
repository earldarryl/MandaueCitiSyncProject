<div class="relative w-full"
     data-component="admin-hr-liaisons-list-view"
     data-wire-id="{{ $this->id() }}">

    <div class="flex flex-col gap-3">
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

            <div class="flex items-center justify-between gap-4 px-4 w-full">
                @php
                    $indexProfile = crc32($department->department_name) % count($palette);
                    $profileBg = $palette[$indexProfile];
                @endphp
                <div class="flex gap-2">
                    <img
                        src="{{ $department->department_profile
                            ? Storage::url($department->department_profile)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) .
                            '&background=' . $profileBg . '&color=fff&size=128' }}"
                        alt="Profile"
                        class="w-16 h-16 rounded-full border border-gray-200 dark:border-zinc-700"
                    >
                    <div class="flex flex-col gap-2">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $department->department_name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $department->department_description }}</p>
                    </div>
                </div>
                <div class="flex justify-end items-end">
                    <button
                        wire:click="loadHrLiaisons"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                            border border-blue-500 dark:border-blue-400
                            hover:bg-blue-200 dark:hover:bg-blue-800/50
                            focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                            transition-all duration-200">
                        <x-heroicon-o-arrow-path class="w-5 h-5" />
                        <span wire:loading.remove wire:target="loadHrLiaisons">Refresh</span>
                        <span wire:loading wire:target="loadHrLiaisons">Processing...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex w-full px-2">
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
                        placeholder="Search HR liaisons..."
                        class="block w-full p-4 ps-10 pe-28 text-sm text-gray-900 border border-gray-300 rounded-lg
                            bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                            dark:bg-zinc-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white
                            dark:focus:outline-none dark:focus:ring-2 dark:focus:ring-blue-400 dark:focus:border-blue-400"
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


        <div wire:loading.remove wire:target="previousPage, nextPage, gotoPage, applySearch">
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

                            <th wire:click="sortBy('status')" class="px-6 py-3 cursor-pointer">
                                <div class="flex items-center justify-between">
                                    <span>Status</span>
                                    <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                        @if($sortField === 'status')
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

                            <th class="px-6 py-3 text-left font-bold">Assignments</th>

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
                                    @php
                                        $status = $liaison->status;

                                        $classes = match ($status) {
                                            'online' => 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500',
                                            'away' => 'bg-yellow-100 text-yellow-800 border-yellow-400 dark:bg-yellow-900/40 dark:text-yellow-300 dark:border-yellow-500',
                                            default => 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600',
                                        };
                                    @endphp

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border shadow-sm {{ $classes }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $liaison->assigned_count }} / {{ $liaison->total_assignments }}
                                </td>

                                <td class="px-6 py-4 text-center space-x-1"
                                    x-data="{
                                        open: false,
                                        openRemove: false,
                                        openEditLiaison: false,
                                    }"
                                    @close-all-modals.window="
                                        open = false;
                                        openRemove = false;
                                        openEditLiaison = false;
                                    "
                                >
                                    <div class="relative inline-block text-left">
                                        <button @click="open = !open"
                                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                                            <x-heroicon-o-ellipsis-horizontal
                                                class="w-6 h-6 text-gray-700 dark:text-gray-200" />
                                        </button>

                                        <div x-show="open" x-transition @click.away="open = false"
                                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-zinc-900
                                                rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 z-50">

                                            <div class="flex flex-col divide-y divide-gray-200 dark:divide-zinc-700">

                                                <a href="{{ route('admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-assignments', [
                                                    'department' => $department->department_id,
                                                    'hrLiaison' => $liaison->id,
                                                ]) }}"
                                                wire:navigate
                                                class="px-4 py-2 font-medium hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm">
                                                    <x-heroicon-o-folder class="w-4 h-4 text-blue-600 dark:text-blue-400"/>
                                                    Manage Assignments
                                                </a>

                                                <button @click="open = false; openEditLiaison = true; $wire.editHrLiaisonModal({{ $liaison->id }}); $wire.resetFields();"
                                                    class="px-4 py-2 font-medium text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm w-full">
                                                    <x-heroicon-o-pencil class="w-4 h-4 text-green-500 dark:text-green-400"/>
                                                    Edit Liaison
                                                </button>

                                                <button @click="open = false; openRemove = true; $wire.resetFields();"
                                                    class="px-4 py-2 font-medium text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm text-red-500 w-full">
                                                    <x-heroicon-o-user-minus class="w-4 h-4"/>
                                                    Remove Liaison
                                                </button>

                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        x-show="openEditLiaison"
                                        x-transition
                                        class="fixed inset-0 flex items-center justify-center z-50 bg-black/20"
                                    >
                                        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md max-h-[90vh] overflow-y-auto flex flex-col"
                                             wire:key="edit-liaisons-{{ $liaison->id }}"
                                        >

                                            <header class="flex gap-2 items-center justify-start border border-gray-300 dark:border-zinc-800 sticky top-0 bg-white dark:bg-zinc-800 z-10 p-3">
                                                <x-heroicon-o-user-circle class="w-6 h-6" />
                                                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 m-0">
                                                    Edit HR Liaison
                                                </h3>
                                            </header>

                                            <div wire:loading wire:target="editHrLiaisonModal({{ $liaison->id }})">
                                                <div class="w-full flex items-center justify-center gap-2 py-6">
                                                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                                </div>
                                            </div>

                                            <div wire:loading.remove wire:target="editHrLiaisonModal({{ $liaison->id }})">
                                                <div class="space-y-3 w-full p-6">
                                                    <flux:input type="text" wire:model.defer="editLiaison.name" placeholder="Full Name" clearable/>
                                                    <flux:error name="editLiaison.name" />

                                                    <flux:input type="email" wire:model.defer="editLiaison.email" placeholder="Email Address" clearable/>
                                                    <flux:error name="editLiaison.email" />

                                                    <flux:input type="password" wire:model.defer="editLiaison.password" placeholder="New Password (Optional)" viewable clearable/>
                                                    <flux:error name="editLiaison.password" />
                                                </div>

                                                <footer class="flex justify-end gap-2 border-t border-gray-300 dark:border-zinc-700 p-3 bg-white dark:bg-zinc-800 sticky bottom-0 z-10 shadow-inner">
                                                    <button @click="openEditLiaison = false; $wire.resetFields();"
                                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100
                                                                dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600 dark:hover:bg-zinc-600/60">
                                                        Cancel
                                                    </button>
                                                    <button wire:click="updateHrLiaison({{ $liaison->id }})" wire:loading.attr="disabled"
                                                            class="px-3 py-1 text-xs rounded-md border border-blue-400 text-white bg-blue-600 hover:bg-blue-700
                                                                dark:bg-blue-900 dark:text-blue-300 dark:border-blue-600 dark:hover:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <span wire:loading.remove wire:target="updateHrLiaison({{ $liaison->id }})">Update</span>
                                                        <span wire:loading wire:target="updateHrLiaison({{ $liaison->id }})">Processing...</span>
                                                    </button>
                                                </footer>
                                            </div>

                                        </div>
                                    </div>

                                    <div x-show="openRemove" x-transition class="fixed inset-0 flex items-center justify-center z-50 bg-black/20">
                                        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-gray-200 dark:border-zinc-700 w-full max-w-md p-6"
                                             wire:key="remove-liaisons-{{ $liaison->id }}"
                                        >
                                            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 mx-auto">
                                                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                                            </div>

                                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Confirm Removal</h2>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Are you sure you want to remove this liaison? This action cannot be undone.</p>

                                            <div wire:loading.remove wire:target="removeLiaison({{ $liaison->id }})" class="flex justify-center gap-3 mt-4">
                                                <button type="button" @click="openRemove = false; $wire.resetFields();"
                                                    class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                                                    Cancel
                                                </button>
                                                <flux:button variant="danger" icon="trash" wire:click="removeLiaison({{ $liaison->id }})">
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
                                <td colspan="6" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                    <x-heroicon-o-user-group class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                                    No HR Liaisons found for this department
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div wire:loading wire:target="previousPage, nextPage, gotoPage, applySearch">
            <div class="mx-6 mt-6 overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 animate-pulse">

                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-100 dark:bg-zinc-900">
                        <tr>
                            @for ($i = 0; $i < 6; $i++)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <div class="h-3 bg-gray-300 dark:bg-zinc-700 rounded w-3/4"></div>
                                </th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @for ($row = 0; $row < 5; $row++)
                            <tr>
                                @for ($col = 0; $col < 6; $col++)
                                    <td class="px-6 py-4 align-middle">
                                        @if($col === 0)
                                            <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
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

        <div class="px-6">
            {{ $hrLiaisons->links() }}
        </div>
    </div>
</div>
