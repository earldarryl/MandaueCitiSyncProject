<div class="w-full z-20 flex flex-col gap-6">
    <!-- User Widget -->
    <div class="w-full mx-auto p-3 shadow-sm sm:rounded-lg">
        <livewire:user-widget-dashboard />
    </div>

    <div class="w-full mx-auto p-3 shadow-sm sm:rounded-lg">
        <livewire:user-grid />
    </div>

    <!-- Citizens Table -->
    <div class="w-full mx-auto p-3 shadow-sm sm:rounded-lg overflow-auto">
        <h1 class="text-2xl font-bold mb-4">Citizens</h1>
        <livewire:user-citizens-table />
    </div>

      <!-- Header -->
    <header class="p-3 shadow">
        <h1 class="text-3xl font-medium flex items-center gap-2">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Dashboard
        </h1>
    </header>

    <!-- Welcome Box -->
    <div class="w-full mx-auto p-3 border border-gray-300 shadow-sm sm:rounded-lg">
        <h1 class="text-2xl font-bold">Welcome, {{ $user->name }}</h1>

        @can('viewAny', $userModel)
            <ul class="list-disc ml-6 mt-4">
                @foreach($users as $u)
                    <li>{{ $u->name }}</li>
                @endforeach
            </ul>
        @endcan
    </div>
</div>
