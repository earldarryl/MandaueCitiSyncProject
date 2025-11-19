import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    scheme: import.meta.env.VITE_REVERB_SCHEME,
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    enabledTransports: [
        import.meta.env.VITE_REVERB_SCHEME === 'https' ? 'wss' : 'ws'
    ],

    authEndpoint: '/broadcasting/auth',
    withCredentials: true,

    auth: {
        headers: {
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        },
    },
});

console.log("Echo initialized for Reverb:", window.Echo);
console.log(window.Laravel.userId);
