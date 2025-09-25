<div class="space-y-6">

    <div class="bg-white shadow-xl rounded-xl p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Grievance Statistics</h2>
    </div>

    <!-- Stats Cards -->
    <div wire:loading.class="opacity-50" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">

        <!-- Resolved Grievances -->
        <div class="bg-white shadow-xl rounded-xl p-6 text-center transition-transform duration-200 hover:scale-105">
            <h3 class="text-4xl font-bold text-green-600">{{ $resolved }}</h3>
            <p class="mt-2 text-sm font-medium text-gray-500">Resolved Grievances</p>
        </div>

        <!-- Pending Grievances -->
        <div class="bg-white shadow-xl rounded-xl p-6 text-center transition-transform duration-200 hover:scale-105">
            <h3 class="text-4xl font-bold text-yellow-600">{{ $pending }}</h3>
            <p class="mt-2 text-sm font-medium text-gray-500">Pending Grievances</p>
        </div>

        <!-- In-Progress Grievances -->
        <div class="bg-white shadow-xl rounded-xl p-6 text-center transition-transform duration-200 hover:scale-105">
            <h3 class="text-4xl font-bold text-blue-600">{{ $inProgress }}</h3>
            <p class="mt-2 text-sm font-medium text-gray-500">In-Progress</p>
        </div>

        <!-- Closed/Rejected Grievances -->
        <div class="bg-white shadow-xl rounded-xl p-6 text-center transition-transform duration-200 hover:scale-105">
            <h3 class="text-4xl font-bold text-red-600">{{ $closed }}</h3>
            <p class="mt-2 text-sm font-medium text-gray-500">Closed/Rejected Grievances</p>
        </div>

        <!-- Overdue Grievances -->
        <div class="bg-white shadow-xl rounded-xl p-6 text-center transition-transform duration-200 hover:scale-105">
            <h3 class="text-4xl font-bold text-purple-600">{{ $overdue }}</h3>
            <p class="mt-2 text-sm font-medium text-gray-500">Overdue Grievances</p>
        </div>

    </div>
</div>
