<div class="w-full mx-auto p-2">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mx-auto px-3 mb-6">

        <div
            class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.calendar class="h-8 w-8 text-blue-600 dark:text-blue-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Account Created</p>
            <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $accountCreated }}</p>
        </div>

        <div
            class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
                border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.building-office class="h-8 w-8 text-green-600 dark:text-green-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Total Departments</p>
            <p class="relative text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">{{ $totalDepartments }}</p>
        </div>

        <div
            class="group relative bg-gradient-to-br from-yellow-50 to-amber-100 dark:from-zinc-800 dark:to-zinc-900
                border border-amber-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-amber-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-amber-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.user-group class="h-8 w-8 text-yellow-500 dark:text-yellow-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Recent Department</p>
            <p class="relative text-3xl font-bold text-yellow-500 dark:text-yellow-400 tracking-tight">
                {{ $recentDepartment ? $recentDepartment->department_name : 'N/A' }}
            </p>
        </div>

    </div>

   @php
        $palette = [
            '0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899',
            '14B8A6','6366F1','F97316','84CC16',
        ];
    @endphp

    @if (empty($departments) || count($departments) === 0)
        <div class="bg-yellow-50 dark:bg-zinc-800 border border-yellow-300 dark:border-zinc-700 rounded-xl p-6 text-center">
            <x-heroicon-o-information-circle class="w-8 h-8 mx-auto text-yellow-500 mb-3" />
            <p class="text-gray-700 dark:text-gray-300 text-lg font-medium">You are not assigned to any department yet.</p>
        </div>
    @else
        <div class="@if(count($departments) === 1) grid grid-cols-1 @else grid sm:grid-cols-2 lg:grid-cols-3 gap-6 @endif">
            @foreach ($departments as $department)
                @php
                    $index = crc32($department->department_name) % count($palette);
                    $bgColor = $palette[$index];

                    $departmentBg = $department->department_bg
                        ? Storage::url($department->department_bg)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) . '&background=' . $bgColor . '&color=fff&size=512';

                    $departmentProfile = $department->department_profile
                        ? Storage::url($department->department_profile)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) . '&background=' . $bgColor . '&color=fff&size=128';
                @endphp

                <div class="group relative bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-sm overflow-hidden">

                    <div class="relative h-32 w-full">
                        <img src="{{ $departmentBg }}" class="w-full h-full object-cover" alt="Department Background">
                        <div class="absolute inset-0 bg-black/25"></div>

                        <div class="absolute bottom-0 left-4 translate-y-1/2">
                            <img src="{{ $departmentProfile }}"
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

                        <div class="flex items-center justify-end">
                            <a href="{{ route('hr-liaison.department.view', $department->department_id) }}" wire:navigate
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-md
                                    bg-blue-100 text-blue-800 border border-blue-300 hover:bg-blue-200
                                    dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700
                                    dark:hover:bg-blue-800/60 transition-all duration-200">
                                <flux:icon.eye class="w-4 h-4" />
                                View
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
