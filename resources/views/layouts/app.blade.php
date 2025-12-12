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

    @elseif(Route::is('two-factor-auth'))
        <div class="flex h-full w-full justify-center items-center">
            {{ $slot }}
        </div>

    @else

    <div class="flex h-full"
        x-data="{
                userId: @js(auth()->id()),
                notyf: null,

                componentMap: @js([
                    'hr-liaison-grievance-index' => 'applyFilters',
                    'hr-liaison-grievance-view' => 'refreshGrievance',
                    'hr-liaison-department-view' => 'refreshHrLiaisonsData',
                    'hr-liaison-department-index' => 'refreshDepartments',
                    'hr-liaison-activity-logs-index' => 'applyFilter',
                    'citizen-grievance-index' => 'applyFilters',
                    'citizen-grievance-view' => 'refreshGrievance',
                    'admin-grievance-index' => 'applyFilters',
                    'admin-grievance-view' => 'refreshGrievance',
                    'admin-feedback-index' => 'applyFilters',
                    'admin-activity-logs-index' => 'applyFilter',
                    'admin-departments-and-hr-liaisons-index' => 'applyFilters',
                    'admin-hr-liaisons-list-view' => 'loadHrLiaisons',
                    'admin-citizens-index' => 'applyFilters',
                    'notifications-live' => 'loadNotifications',
                ]),

                initNotyf() {
                    if (!this.notyf) {
                        const localNotyf = new Notyf({
                            duration: 5000,
                            position: { x: 'right', y: 'top' },
                            dismissible: true,
                            ripple: true,
                            types: [
                                { type: 'info', background: '#2196F3' },
                                { type: 'warning', background: '#FF9800' },
                                { type: 'success', background: '#4CAF50' },
                                { type: 'error', background: 'indianred', duration: 4000 }
                            ]
                        });

                        this.notyf = localNotyf;

                        document.addEventListener('notify', (event) => {
                            const detail = Array.isArray(event.detail) ? (event.detail[0] ?? {}) : (event.detail ?? {});
                            const type = detail.type ?? 'info';
                            const title = detail.title ?? '';
                            const message = detail.message ?? '';

                            localNotyf.open({
                                type: type,
                                message: `<b>${title}</b><br>${message}`,
                                icon: {
                                    className: 'material-icons',
                                    tagName: 'i',
                                    text:
                                        type === 'success' ? 'check_circle' :
                                        type === 'error'   ? 'error' :
                                        type === 'warning' ? 'warning' :
                                        'info',
                                    color: '#ffffff'
                                }
                            });
                        });

                        this.initNotifications(localNotyf);
                    }
                },

                initNotifications(localNotyf) {
                    if (!this.userId || !window.Echo) return;

                    Echo.private(`App.Models.User.${this.userId}`)
                        .notification((notification) => {

                            const payload = notification.data || notification.notification || notification;
                            if (!payload) return;

                            const type = payload.metadata?.type || 'info';
                            const title = payload.title ?? 'New Notification';
                            const body  = payload.body ?? '';
                            const duration = type === 'error' ? 4000 : 5000;

                            localNotyf.open({
                                type: type,
                                message: `<b>${title}</b><br>${body}`,
                                duration: duration,
                                icon: {
                                    className: 'material-icons',
                                    tagName: 'i',
                                    color: '#fff',
                                    text:
                                        type === 'success' ? 'check_circle' :
                                        type === 'error'   ? 'error' :
                                        type === 'warning' ? 'warning' :
                                        'info',
                                }
                            });

                            if (Array.isArray(payload.actions)) {
                                payload.actions.forEach((action) => {
                                    setTimeout(() => {
                                        if (action.url && action.open_new_tab) {
                                            window.open(action.url, '_blank');
                                        }
                                        if (action.dispatch && window.Livewire) {
                                            Livewire.dispatch(action.dispatch);
                                        }
                                    }, 1000);
                                });
                            }

                            Object.entries(this.componentMap).forEach(([selector, method]) => {
                                const el = document.querySelector(`[data-component='${selector}']`);

                                if (el?.dataset?.wireId) {
                                    const instance = Livewire.find(el.dataset.wireId);
                                    if (instance && typeof instance.$call === 'function') {
                                        instance.$call(method);
                                    }
                                }
                            });
                        });
                }
            }"
            x-init="initNotyf()"
        >

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
        document.addEventListener('alpine:init', () => {
        Alpine.data('progressLogs', (initialCanLoadMore) => ({
            loadingMore: false,
            canLoadMore: initialCanLoadMore,
            initialized: false,
            prevScrollTop: 0,

            init() {
            this.initialized = true;

            const el = this.$refs.logContainer;
            if (!el) return;

            el.addEventListener('scroll', () => this.checkScroll());
            },

            emitToLivewire(eventName, payload = null) {
            if (window.Livewire && typeof window.Livewire.emit === 'function') {
                return window.Livewire.emit(eventName, payload);
            }
            if (window.livewire && typeof window.livewire.emit === 'function') {
                return window.livewire.emit(eventName, payload);
            }

            return new Promise(resolve => {
                const onLoad = () => {
                if (window.Livewire && typeof window.Livewire.emit === 'function') {
                    window.Livewire.emit(eventName, payload);
                } else if (window.livewire && typeof window.livewire.emit === 'function') {
                    window.livewire.emit(eventName, payload);
                } else {
                    window.dispatchEvent(new CustomEvent(eventName, { detail: payload }));
                }
                resolve();
                };

                window.addEventListener('livewire:load', onLoad, { once: true });

                setTimeout(() => {
                if (window.Livewire || window.livewire) onLoad();
                }, 2000);
            });
            },

            scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.logContainer;
                if (!el) return;
                el.scrollTop = el.scrollHeight;
            });
            },

            checkScroll() {
            const el = this.$refs.logContainer;
            if (!el || !this.initialized) return;

            if (el.scrollTop < this.prevScrollTop && el.scrollTop <= 5 && !this.loadingMore && this.canLoadMore) {
                this.loadingMore = true;

                const prevScrollTop = el.scrollTop;
                const prevScrollHeight = el.scrollHeight;

                const onUpdated = (event) => {
                this.$nextTick(() => {
                    const newScrollHeight = el.scrollHeight;
                    el.scrollTop = prevScrollTop + (newScrollHeight - prevScrollHeight);
                    this.loadingMore = false;

                    const newCanLoadMore = event.detail.canLoadMore !== undefined
                    ? event.detail.canLoadMore
                    : (event.detail[0] && event.detail[0].canLoadMore);

                    if (newCanLoadMore !== undefined) {
                    this.canLoadMore = newCanLoadMore;
                    }
                });

                window.removeEventListener('remarks-updated', onUpdated);
                };

                window.addEventListener('remarks-updated', onUpdated);

                this.emitToLivewire('loadMore').catch(() => {
                this.loadingMore = false;
                window.removeEventListener('remarks-updated', onUpdated);
                });
            }

            this.prevScrollTop = el.scrollTop;
            }
        }));
        });
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatScroll', (initialCanLoadMore) => ({
                hasMore: initialCanLoadMore,
                loadingOlder: false,
                initialized: false,

                prevScrollHeight: 0,

                checkScroll() {
                    const el = this.$refs.chatBox;
                    if (!el || !this.hasMore || this.loadingOlder || !this.initialized) return;

                    if (Math.ceil(el.scrollTop) === 0) {
                        this.loadingOlder = true;

                        this.prevScrollHeight = el.scrollHeight;

                        const onUpdated = (event) => {
                            this.hasMore = event.detail.canLoadMore;

                            this.$nextTick(() => {
                                const newScrollHeight = el.scrollHeight;
                                el.scrollTop = newScrollHeight - this.prevScrollHeight;
                                this.loadingOlder = false;
                            });

                            window.removeEventListener('messagesLoaded', onUpdated);
                        };

                        window.addEventListener('messagesLoaded', onUpdated);

                        this.$wire.loadOlderMessages();
                    }
                },

                init() {
                    const el = this.$refs.chatBox;
                    if (!el) return;

                    this.initialized = true;
                    this.hasMore = initialCanLoadMore;

                    el.addEventListener('scroll', () => this.checkScroll());

                    this.$nextTick(() => {
                        if (el.scrollHeight > el.clientHeight) {
                            el.scrollTop = el.scrollHeight - el.clientHeight;
                        }
                    });

                    window.addEventListener('messageReceived', (event) => {
                        const distanceFromBottom = el.scrollHeight - el.scrollTop - el.clientHeight;

                        if (distanceFromBottom < 100) {
                            this.$nextTick(() => {
                                el.scrollTop = el.scrollHeight - el.clientHeight;
                            });
                        }
                    });
                }
            }));
        });
    </script>

    @vite('resources/js/pusher-echo.js')
    @filamentScripts
    @fluxScripts

    @endif
</body>
</html>
