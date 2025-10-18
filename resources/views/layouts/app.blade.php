<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Shared head content --}}
    @include('partials.header')

    <title>{{ $title ?? 'My App' }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Styles --}}
    @livewireStyles
    @filamentStyles
    @fluxAppearance
</head>
<body class="font-sans antialiased animate-fadeIn">

    @livewire('notifications')


    @if(Route::is('password.confirm'))
        <div class="flex h-full w-full justify-center items-center">
            {{ $slot }}
        </div>
    @else
    <div class="flex h-full">

        <livewire:partials.sidebar />
        <livewire:partials.notifications />

        <div x-data class="relative flex flex-col flex-1 h-full overflow-y-auto overflow-x-auto">
            <div
                x-show="($store.sidebar.open && $store.sidebar.screen < 1024) || $store.notifications.open"
                x-transition.opacity
                class="fixed inset-0 bg-black/50 z-[35]"
                @click="
                    if ($store.sidebar.screen < 1024) {
                        $store.sidebar.open = false
                    }
                    $store.notifications.open = false
                "
            ></div>

            <livewire:partials.navigation />
            <livewire:partials.breadcrumbs/>

            <div class="flex-1 flex">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Logout modal --}}
    <livewire:pages.auth.logout />



    {{-- Scripts --}}
    @livewireScripts
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            userId: {{ auth()->id() ?? 'null' }},
        };
    </script>
    @vite('resources/js/echo.js')
    @filamentScripts
    @fluxScripts

    @endif
</body>
</html>
