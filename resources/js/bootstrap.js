console.log('BOOTSTRAP JS LOADED');

import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Pusher
window.Pusher = Pusher;

// Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

console.log('Echo initialized', window.Echo);

// Listener de notificações
function addNotificationToUI(notification) {
    const list = document.getElementById('notifications-list');
    if (!list) return;

    const item = document.createElement('li');
    item.innerHTML = `
        <strong>${notification.title}</strong>
        <p>${notification.message}</p>
    `;

    list.prepend(item);
}

if (window.USER_ID) {
    window.Echo
        .channel(`user.${window.USER_ID}`)
        .listen('.notification.created', (e) => {
            // console.log('Nova notificação:', e.notification);
            // addNotificationToUI(e.notification);
            showToast(e.notification);
        });
}

function showToast(notification) {
    const toastEl = document.getElementById('notificationToast');
    const toastBody = document.getElementById('toastBody');

    if (!toastEl || !toastBody) return;

    toastBody.innerHTML = `
        <strong>${notification.title}</strong><br>
        ${notification.message}
    `;

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

