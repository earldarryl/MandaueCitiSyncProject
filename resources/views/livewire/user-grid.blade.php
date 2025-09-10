<div class="p-6">
    <!-- Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <!-- Search -->
        <flux:input
            type="text"
            wire:model.live="search"
            placeholder="Search users..."
        />

        <!-- Status Filter -->
        <select
            wire:model="status"
            class="w-full md:w-1/4 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
        >
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <!-- Role Filter -->
        <select
            wire:model.live="role"
            class="w-full md:w-1/4 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
        >
            <option value="all">All Roles</option>
            @foreach($roles as $roleName)
                <option value="{{ $roleName }}">{{ ucfirst($roleName) }}</option>
            @endforeach
        </select>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading.delay
         class="absolute inset-0 bg-white/80 dark:bg-gray-900/80 flex items-center justify-center z-50 rounded-lg">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
    </div>

    <!-- User Grid (2x3 cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 relative">
        @forelse($users as $user)
            <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-4 transition hover:shadow-lg">
                <div class="flex items-center gap-3">
                    <!-- Avatar placeholder -->
                    <div class="h-12 w-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $user->name }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <!-- Roles -->
                    <span class="text-xs font-medium px-3 py-1 rounded-full bg-blue-100 text-blue-700">
                        {{ $user->roles->pluck('name')->join(', ') ?: 'No Role' }}
                    </span>

                    <!-- Status -->
                    <span class="text-xs font-medium px-3 py-1 rounded-full
                        {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                        {{ ucfirst($user->status ?? 'inactive') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500 dark:text-gray-400">
                No users found.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
