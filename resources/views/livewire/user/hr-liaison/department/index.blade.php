<div class="w-full mx-auto p-2">

    <div class="w-full grid grid-cols-2 md:grid-cols-3 gap-4 mx-auto px-3 mb-6">

        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                    border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm
                    hover:shadow-lg hover:brightness-105 hover:scale-105
                    transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                        dark:border-zinc-700 group-hover:scale-110 group-hover:shadow-lg transition-transform duration-300">
                <flux:icon.calendar class="h-8 w-8 text-blue-600 dark:text-blue-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Account Created</p>
            <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $accountCreated }}</p>
        </div>

        <div class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
                    border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm
                    hover:shadow-lg hover:brightness-105 hover:scale-105
                    transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                        dark:border-zinc-700 group-hover:scale-110 group-hover:shadow-lg transition-transform duration-300">
                <flux:icon.building-office class="h-8 w-8 text-green-600 dark:text-green-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Total Departments</p>
            <p class="relative text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">{{ $totalDepartments }}</p>
        </div>

        <div class="group relative bg-gradient-to-br from-yellow-50 to-amber-100 dark:from-zinc-800 dark:to-zinc-900
                    border border-amber-200/50 dark:border-zinc-700 rounded-2xl shadow-sm
                    hover:shadow-lg hover:brightness-105 hover:scale-105
                    transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-amber-200/50
                        dark:border-zinc-700 group-hover:scale-110 group-hover:shadow-lg transition-transform duration-300">
                <flux:icon.user-group class="h-8 w-8 text-yellow-500 dark:text-yellow-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Recent Department</p>
            <p class="relative text-3xl font-bold text-yellow-500 dark:text-yellow-400 tracking-tight">
                {{ $recentDepartment ? $recentDepartment->department_name : 'N/A' }}
            </p>
        </div>

    </div>

  @if (empty($departments))
        <div class="bg-yellow-50 dark:bg-zinc-800 border border-yellow-300 dark:border-zinc-700 rounded-xl p-6 text-center">
            <x-heroicon-o-information-circle class="w-8 h-8 mx-auto text-yellow-500 mb-3" />
            <p class="text-gray-700 dark:text-gray-300 text-lg font-medium">You are not assigned to any department yet.</p>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($departments as $department)
                <div class="group relative bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-sm
                            hover:shadow-lg hover:brightness-105 hover:scale-105 transition-all duration-300 overflow-hidden">

                    <div class="relative h-32 w-full">
                        <img src="{{ $department->department_bg_url }}"
                            class="w-full h-full object-cover"
                            alt="Department Background">
                        <div class="absolute inset-0 bg-black/25"></div>

                        <div class="absolute bottom-0 left-4 translate-y-1/2">
                            <img src="{{ $department->department_profile_url }}"
                                class="w-16 h-16 rounded-full border-4 border-white shadow-md object-cover"
                                alt="Department Profile">
                        </div>
                    </div>

                    <div class="pt-10 pb-4 px-5 flex flex-col gap-2">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $department->department_name }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                            {{ $department->department_description ?? 'No description available.' }}
                        </p>

                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-auto pt-2 border-t border-gray-200 dark:border-zinc-700">
                            <span>Code: <span class="font-medium">{{ $department->department_code }}</span></span>
                            <span>Created: {{ $department->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
