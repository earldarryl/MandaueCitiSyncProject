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

<body class="w-full h-full antialiased" @show-register-modal.window="console.log('Event caught!'); $store.modal.show();">
 <div class="absolute top-0 left-0 z-50" x-data="{ dark: $flux.dark }" x-effect="dark = $flux.dark">
    <template x-if="dark">
        <flux:button
            x-on:click="$flux.dark = false"
            icon="moon"
            variant="ghost"
            aria-label="Switch to light mode"
            class="border-none"
        />
    </template>

    <template x-if="!dark">
        <flux:button
            x-on:click="$flux.dark = true"
            icon="sun"
            variant="primary"
            aria-label="Switch to dark mode"
            class="border-none bg-mc_primary_color text-white lg:bg-white lg:text-black hover:bg-mc_primary_color lg:hover:bg-gray-200"
        />
    </template>
</div>
    @if(Route::is('login'))
        <div x-data>
            <div class="h-full w-full flex flex-col-reverse lg:grid lg:grid-cols-3 items-center justify-center">
                <div class="bg-white dark:bg-zinc-900 w-full lg:col-span-1 flex items-center justify-center h-full">
                    <div class="w-full h-full px-5 py-5 flex flex-col items-center justify-center rounded-lg overflow-hidden">
                        {{ $slot }}
                    </div>
                </div>

                <div class="bg-mc_primary_color dark:bg-zinc-900 w-full lg:col-span-2 flex flex-col items-center justify-center text-start min-h-[25vh] lg:min-h-screen select-none">
                    <h1 class="tracking-tighter archivo-black-regular text-white dark:text-blue-600 font-extrabold uppercase inline-flex flex-col justify-start">
                        <span class="block leading-[0.8] text-6xl lg:text-9xl">Mandaue</span>
                        <span class="block leading-[0.8] text-6xl lg:text-9xl">Citi-Sync</span>
                    </h1>
                </div>
            </div>

            <!-- Modal Wrapper -->
            <livewire:pages.auth.register />

        </div>
    @endif
    @if(Route::is('hr-liaison.login'))
        <div x-data>
            <div class="h-full w-full flex flex-col-reverse lg:grid lg:grid-cols-3 items-center justify-center">
                <div class="bg-white dark:bg-zinc-900 w-full lg:col-span-1 flex items-center justify-center h-full">
                    <div class="w-full h-full px-5 py-5 flex flex-col items-center justify-center rounded-lg overflow-hidden">
                        {{ $slot }}
                    </div>
                </div>

                <div class="bg-mc_primary_color dark:bg-zinc-900 w-full lg:col-span-2 flex flex-col items-center justify-center text-start min-h-[25vh] lg:min-h-screen select-none">
                    <h1 class="tracking-tighter archivo-black-regular text-white dark:text-blue-600 font-extrabold uppercase inline-flex flex-col justify-start">
                        <span class="block leading-[0.8] text-6xl sm:text-7xl md:text-8xl lg:text-9xl">Mandaue</span>
                        <span class="block leading-[0.8] text-6xl sm:text-7xl md:text-8xl lg:text-9xl">Citi-Sync</span>
                    </h1>
                </div>
            </div>
        </div>
    @endif
    @if(Route::is('admin.login'))
        <div x-data>
            <div class="h-full w-full flex flex-col-reverse lg:grid lg:grid-cols-3 items-center justify-center">
                <div class="bg-white dark:bg-zinc-900 w-full lg:col-span-1 flex items-center justify-center h-full">
                    <div class="w-full h-full px-5 py-5 flex flex-col items-center justify-center rounded-lg overflow-hidden">
                        {{ $slot }}
                    </div>
                </div>

                <div class="bg-mc_primary_color dark:bg-zinc-900 w-full lg:col-span-2 flex flex-col items-center justify-center text-start min-h-[25vh] lg:min-h-screen select-none">
                    <h1 class="tracking-tighter archivo-black-regular text-white dark:text-blue-600 font-extrabold uppercase inline-flex flex-col justify-start">
                        <span class="block leading-[0.8] text-6xl sm:text-7xl md:text-8xl lg:text-9xl">Mandaue</span>
                        <span class="block leading-[0.8] text-6xl sm:text-7xl md:text-8xl lg:text-9xl">Citi-Sync</span>
                    </h1>
                </div>
            </div>
        </div>
    @endif
    @if(Route::is('verification.notice'))
        <div class="h-full w-full flex flex-col-reverse lg:grid lg:grid-cols-3 items-center justify-center">
            <div class="w-full lg:col-span-1 flex items-center justify-center h-full">
                <div class="w-full h-full px-5 py-5 flex flex-col items-center justify-center overflow-hidden">
                    {{ $slot }}
                </div>
            </div>

            <div class="w-full lg:col-span-2 flex items-center justify-center text-center h-full">
                <img
                    src="{{ asset('/images/email-pic.png') }}"
                    class="max-w-full h-auto sm:max-w-1/2 md:max-w-2/3 lg:max-w-full"
                    alt="img"
                >
            </div>
        </div>
    @endif

    @if(Route::is('password.request') || Route::is('password.reset'))
        <div class="h-full w-full flex flex-col-reverse lg:grid lg:grid-cols-3 items-center justify-center bg-white dark:bg-black">
            <div class="w-full lg:col-span-1 flex items-center justify-center h-full">
                <div class="w-full h-full px-5 py-5 flex flex-col items-center justify-center overflow-hidden bg-white dark:bg-black rounded-lg md:rounded-none lg:rounded-none">
                    {{ $slot }}
                </div>
            </div>

            <div class="w-full lg:col-span-2 flex items-center justify-center text-center h-1/3">
                <img
                    src="{{ asset('/images/faq-img.png') }}"
                    class="max-w-full h-auto sm:max-w-1/2 md:max-w-2/3 lg:max-w-full"
                    alt="img"
                >
            </div>
        </div>

    @endif
</body>
@livewireScripts
@filamentScripts
@fluxScripts
<script>
    document.addEventListener('registration-success', () => {
        console.log('Registration Success');
        $store.modal.hide();
        Livewire.dispatch('reset-register-form');
    });
</script>
<script>
    Livewire.on('register-finished', () => {
        document.querySelector('[x-data]').__x.$data.loading = false;
    });
</script>
</html>
