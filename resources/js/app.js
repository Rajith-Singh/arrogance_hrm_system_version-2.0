import './bootstrap';

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Listen for chat messages in a private channel
window.Echo.private(`chat.${userId}`)
    .listen('ChatMessageSent', (e) => {
        console.log(e.message);
        // Update your chat interface with the new message
    });

