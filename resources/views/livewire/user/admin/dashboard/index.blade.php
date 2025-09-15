<div class="w-full z-20 flex flex-col gap-6">

    <!-- Header -->
    <header class="p-4 bg-mc_primary_color dark:bg-blue-500 shadow-md">
        <h1 class="text-3xl font-semibold text-white flex items-center gap-2">
            <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875
                         c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504
                         1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Dashboard
        </h1>
    </header>

    <!-- Stats Widgets -->
    <div class="w-full mx-auto p-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <livewire:dashboard-widgets />
        </div>
    </div>

    <!-- Welcome Box -->
    <div class="w-full mx-auto p-6 shadow-md">
        <h1 class="text-2xl font-bold">
            Welcome back, <span class="text-blue-500">{{ $user->name }}</span>
        </h1>
        <p class="mt-2">Here’s what’s happening in the system today:</p>

        @can('viewAny', $userModel)
            <div class="mt-4">
                <livewire:dashboard-table/>
            </div>
        @endcan
    </div>
</div>
