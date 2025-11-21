@php
    $currentPage = match (true) {
        request()->routeIs('settings.profile') => 'profile',
        request()->routeIs('settings.two-factor-auth') => 'two-factor-auth',
        request()->routeIs('settings') => 'profile',
        default => 'profile',
    };
@endphp

<div
    x-data="{
        activePage: '{{ $currentPage }}',
        loading: false,
        init() {
            const path = window.location.pathname.split('/').pop();
            if (['profile', 'two-factor-auth'].includes(path)) {
                this.activePage = path;
            }
            window.addEventListener('popstate', () => {
                const newPath = window.location.pathname.split('/').pop();
                if (['profile', 'two-factor-auth'].includes(newPath)) {
                    this.activePage = newPath;
                }
            });
        }
    }"
    x-on:set-page.window="
        loading = true;
        activePage = $event.detail;
        $wire.dispatch('reset-form');
    "
    x-on:reset-finished.window="loading = false"
    class="flex flex-col lg:flex-row h-full w-screen bg-white dark:bg-black"
    x-cloak
>
    <livewire:partials.mini-sidebar />

    <div class="flex-1 flex flex-col overflow-hidden h-full">
        <div
            x-show="loading"
            class="absolute inset-0 flex items-center h-full justify-center z-30 bg-white/70 dark:bg-black/70"
        >
            <div
                class="bg-gray-200 dark:bg-zinc-800 shadow-md
                    flex flex-col items-center justify-center gap-3
                    px-6 py-4 w-full h-full"
            >
                <span class="animate-spin rounded-full h-8 w-8 border-2 border-gray-600 border-t-transparent"></span>
                <span class="font-medium text-gray-700 dark:text-gray-200">Loading...</span>
            </div>
        </div>

        <div x-show="!loading" x-cloak class="flex-1 overflow-auto h-full">
            <div x-show="activePage === 'profile'" x-cloak class="h-full w-full flex flex-col">
                <livewire:settings.profile class="flex-1 w-full" />
            </div>
            <div x-show="activePage === 'two-factor-auth'" x-cloak class="h-full w-full flex flex-col">
                <livewire:settings.two-factor-auth class="flex-1 w-full" />
            </div>
        </div>
    </div>
</div>
