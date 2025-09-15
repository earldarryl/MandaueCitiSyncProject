<div class="p-6 space-y-6 relative">

    {{-- 🔄 Loading Overlay --}}
    <div wire:loading.flex
         class="absolute inset-0 bg-white/70 dark:bg-zinc-900/70 z-50 items-center justify-center rounded-lg">
        <div class="animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-blue-500"></div>
    </div>

    {{-- 🔍 Filters + Sorting --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-3">
            <input type="text" wire:model.debounce.500ms="search"
                   placeholder="Search users..."
                   class="border rounded px-3 py-2 text-sm">

            <select wire:model="status" class="border rounded px-3 py-2 text-sm">
                <option value="all">All Status</option>
                <option value="online">Online</option>
                <option value="away">Away</option>
                <option value="offline">Offline</option>
            </select>

            <select wire:model="role" class="border rounded px-3 py-2 text-sm">
                <option value="all">All Roles</option>
                @foreach ($roles as $roleName)
                    <option value="{{ $roleName }}">
                        {{ ucwords(str_replace('_', ' ', $roleName)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3">
            <button wire:click="sortBy('name')" class="px-3 py-2 border rounded text-sm">
                Sort by Name
                @if ($sortField === 'name')
                    {{ $sortDirection === 'asc' ? '⬆️' : '⬇️' }}
                @endif
            </button>
            <button wire:click="sortBy('created_at')" class="px-3 py-2 border rounded text-sm">
                Sort by Created
                @if ($sortField === 'created_at')
                    {{ $sortDirection === 'asc' ? '⬆️' : '⬇️' }}
                @endif
            </button>
        </div>
    </div>

    {{-- 📑 User Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($users as $user)
            <div wire:key="user-{{ $user->id }}"
                 class="p-4 border rounded-lg shadow-sm bg-white dark:bg-zinc-800">
                <div class="flex items-center space-x-4">
                    <img src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : asset('images/avatar.png') }}"
                         class="w-12 h-12 rounded-full object-cover" alt="{{ $user->name }}">
                    <div>
                        <h3 class="font-semibold">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <p class="text-xs text-gray-400">{{ $user->contact ?? 'No contact' }}</p>
                    </div>
                </div>

                <div class="mt-3 text-sm">
                    <p>Status:
                        <span class="{{ $user->status === 'online' ? 'text-green-500' : ($user->status === 'away' ? 'text-yellow-500' : 'text-red-500') }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </p>
                    <p>Last Seen: {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never' }}</p>
                    <p>Agreed Terms:
                        {{ $user->agreed_terms
                            ? '✅ (v' . $user->terms_version . ' on ' . $user->agreed_at->format('Y-m-d') . ')'
                            : '❌' }}
                    </p>
                    <p>Roles:
                        {{ $user->getRoleNames()
                                ->map(fn($role) => ucwords(str_replace('_', ' ', $role)))
                                ->implode(', ')
                                ?: 'No role'
                        }}
                    </p>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-500">No users found.</p>
        @endforelse
    </div>

    {{-- 📌 Pagination --}}
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
