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

    <div class="flex h-full">
        <livewire:partials.sidebar />

        <div x-data class="relative flex flex-col flex-1 h-full overflow-y-auto overflow-x-auto">
            <!-- Overlay only in content container -->
            <div
                x-show="$store.sidebar.open && $store.sidebar.screen <= 768"
                x-transition.opacity
                class="fixed inset-0 bg-black/50 z-[35] lg:hidden"
                @click="$store.sidebar.open = false"
            ></div>

            <livewire:partials.navigation />

            <div class="flex-1 flex">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Logout modal --}}
    <livewire:pages.auth.logout />

    {{-- Scripts --}}
    @livewireScripts
    @filamentScripts
    @fluxScripts
</body>
</html>
