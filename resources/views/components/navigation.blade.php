<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <h1 class="text-lg font-bold">MyApp</h1>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('profile') }}" class="text-gray-700 hover:text-gray-900 font-medium">
                        Profile
                    </a>
                </div>
            </div>

            <!-- Logout Button -->
            <div class="hidden sm:flex sm:items-center">
                <button wire:click="logout" class="text-red-500">Logout</button>
            </div>
        </div>
    </div>
</nav>
