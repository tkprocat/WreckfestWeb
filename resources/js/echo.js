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

console.log('Echo initialized with config:', {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
});

// Debug: Listen to connection events
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('✓ Echo: Successfully connected to Reverb');
});

window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('✗ Echo: Connection error', err);
});

window.Echo.connector.pusher.connection.bind('state_change', (states) => {
    console.log('Echo: Connection state changed from', states.previous, 'to', states.current);
});

// Debug: Test subscribing to server-updates channel
const channel = window.Echo.channel('server-updates');
console.log('Echo: Subscribing to server-updates channel');

channel.listen('.players.updated', (event) => {
    console.log('Echo: Received players.updated event (direct listener):', event);
});
