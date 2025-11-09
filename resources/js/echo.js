import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Log WebSocket connection state changes
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('%c✓ WebSocket connected successfully', 'color: green; font-weight: bold');
    console.log('Connected to:', import.meta.env.VITE_REVERB_SCHEME + '://' + import.meta.env.VITE_REVERB_HOST + ':' + import.meta.env.VITE_REVERB_PORT);
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('%c✗ WebSocket disconnected', 'color: orange; font-weight: bold');
});

window.Echo.connector.pusher.connection.bind('failed', () => {
    console.log('%c✗ WebSocket connection failed', 'color: red; font-weight: bold');
});

window.Echo.connector.pusher.connection.bind('unavailable', () => {
    console.log('%c✗ WebSocket connection unavailable', 'color: red; font-weight: bold');
});
