<div
    x-data="{ open: false }"
    @registration-success.window="open = true;"
    x-cloak
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center"
    >
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-black/50"
    ></div>

    <div
        x-show="open"
        x-transition
        class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg max-w-md w-full p-6 flex flex-col items-center gap-4 z-50"
    >
        <div class="relative">
            <img
                src="{{ asset('/images/check.png') }}"
                class="w-full h-48 sm:h-56 object-cover"
                alt="Registration Success Background"
            >
        </div>

        <div class="flex flex-col items-center space-y-2 w-full">
            <span class="text-4xl font-bold text-blue-600">Registration Complete</span>
            <span class="text-md font-semibold text-center text-gray-700 dark:text-gray-200">
                You have successfully registered your account!
            </span>
            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400 text-center">
                Please wait a moment...
            </span>
        </div>

        <div class="flex items-center justify-center gap-2">
            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full animate-bounce [animation-delay:0s]"></div>
            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full animate-bounce [animation-delay:0.3s]"></div>
            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full animate-bounce [animation-delay:0.6s]"></div>
        </div>
    </div>
</div>
