// resources/js/bootstrap.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Ensure Pusher is available globally
window.Pusher = Pusher;

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: "my-app-key",
    wsHost: '51.20.56.40',
    wsPort: 6001,
    wssPort: 443,
    forceTLS: false,
    enabledTransports: ['ws'],
    authorizer: (channel) => {
        return {
            authorize: (socketId, callback) => {
                callback(false, {});
            }
        };
    }
});

console.log('Echo initialized in bootstrap');
