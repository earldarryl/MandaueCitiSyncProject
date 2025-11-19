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

            <div class="flex-1 flex">
                {{ $slot }}
            </div>
        </div>
    </div>

    <livewire:pages.auth.logout />

    @livewireScripts
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            userId: {{ auth()->id() ?? 'null' }},
        };
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userId = @json(auth()->id());

            if (window.Echo && userId) {
                Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {

                        console.log('Incoming notification', notification);

                        const f = new FilamentNotification()
                            .title(notification.title || 'New Notification')
                            .body(notification.body || '');

                        switch (notification.type) {
                            case 'success': f.success(); break;
                            case 'warning': f.warning(); break;
                            case 'danger':  f.danger();  break;
                            default:        f.info();    break;
                        }

                        if (notification.actions && Array.isArray(notification.actions)) {
                            const builtActions = notification.actions.map(action => {
                                let btn = new FilamentNotificationAction(action.label);

                                if (action.button !== false) {
                                    btn = btn.button();
                                }

                                if (action.url) {
                                    btn = btn.url(action.url);
                                }

                                if (action.open_new_tab) {
                                    btn = btn.openUrlInNewTab();
                                }

                                if (action.color) {
                                    btn = btn.color(action.color);
                                }

                                if (action.dispatch) {
                                    btn = btn.dispatch(action.dispatch);
                                }

                                if (action.close) {
                                    btn = btn.close();
                                }

                                return btn;
                            });

                            f.actions(builtActions);
                        }

                        f.send();
                    });
            }
        });
    </script>
    @vite('resources/js/pusher-echo.js')
    @filamentScripts
    @fluxScripts

    @endif
</body>
</html>
