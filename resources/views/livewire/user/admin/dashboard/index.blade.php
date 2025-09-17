<div class="w-full flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900">

    <!-- Header -->
    <header class="p-6 flex flex-col gap-2 bg-sky-500/10 rounded-b-lg shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-sky-900 dark:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875
                        c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504
                        1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <h1 class="text-3xl font-semibold text-sky-900 dark:text-blue-500">Dashboard</h1>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Welcome back, <span class="text-sky-900 dark:text-blue-500">{{ $user->name }}</span>
            </h2>
            <p class="mt-1 text-gray-600 dark:text-gray-300">Here’s what’s happening in the system today:</p>
        </div>
    </header>

    <!-- Stats Widgets -->
    <section class="w-full p-4">
        <div class="flex">
            <livewire:user.admin.dashboard.custom-stats />
        </div>
    </section>

    <!-- Chart Widgets -->
    <section class="w-full flex justify-center gap-6 bg-white dark:bg-zinc-800 p-3">
            <div class="rounded-lg shadow flex items-center justify-center">
                <livewire:chart-widget />
            </div>
            <div class="rounded-lg shadow flex items-center justify-center">
                <livewire:pie-widget />
            </div>
    </section>

    <section class="w-full p-4 mt-4 bg-white dark:bg-zinc-800">
        @can('viewAny', $userModel)
            <div class="shadow-sm rounded-lg p-4">
                <livewire:dashboard-table/>
            </div>
        @endcan
    </section>

</div>
