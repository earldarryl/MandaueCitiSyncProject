<div class="w-full mx-auto py-10 px-6">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">
        My Assigned Departments
    </h1>

    @if (empty($departments))
        <div class="bg-yellow-50 dark:bg-zinc-800 border border-yellow-300 dark:border-zinc-700 rounded-lg p-6 text-center">
            <x-heroicon-o-information-circle class="w-6 h-6 mx-auto text-yellow-500 mb-2" />
            <p class="text-gray-700 dark:text-gray-300">You are not assigned to any department yet.</p>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($departments as $department)
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden transition hover:shadow-md">

                    <!-- Department Header Background -->
                    <div class="relative h-32">
                        <img src="{{ $department->department_bg_url }}"
                             class="w-full h-full object-cover"
                             alt="Department Background">
                        <div class="absolute inset-0 bg-black/30"></div>

                        <!-- Department Profile (logo/avatar) -->
                        <div class="absolute bottom-0 left-4 translate-y-1/2">
                            <img src="{{ $department->department_profile_url }}"
                                 class="w-16 h-16 rounded-full border-4 border-white shadow-md object-cover"
                                 alt="Department Profile">
                        </div>
                    </div>

                    <div class="pt-10 pb-4 px-5">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                            {{ $department->department_name }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            {{ $department->department_description ?? 'No description available.' }}
                        </p>

                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Code: <span class="font-semibold">{{ $department->department_code }}</span></span>
                            <span class="{{ $department->is_active ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <!-- Upload form -->
                        <div class="mt-4 border-t border-gray-200 dark:border-zinc-700 pt-3">
                            <form wire:submit.prevent="updatePhoto({{ $department->department_id }})" class="space-y-2">
                                <div class="flex flex-col gap-2">
                                    <label class="text-xs font-medium">Background Image</label>
                                    <input type="file" wire:model="bgImage.{{ $department->department_id }}" class="text-sm">
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-xs font-medium">Profile Picture</label>
                                    <input type="file" wire:model="profileImage.{{ $department->department_id }}" class="text-sm">
                                </div>

                                <button type="submit"
                                    class="mt-2 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-2 rounded-lg">
                                    Update Photos
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if (session('success'))
        <div class="mt-6 bg-green-50 border border-green-300 text-green-700 rounded-lg p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif
</div>
