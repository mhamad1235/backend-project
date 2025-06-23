// resources/js/bootstrap.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Ensure Pusher is available globally
window.Pusher = Pusher;

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: "my-app-key",
    wsHost: '13.60.50.148',
    wsPort: 6001,
    wssPort: 443,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    authorizer: (channel) => {
        return {
            authorize: (socketId, callback) => {
                callback(false, {});
            }
        };
    }
});

console.log('Echo initialized in bootstrap');
