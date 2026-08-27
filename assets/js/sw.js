// Listen for messages to show notifications
self.addEventListener('message', (event) => {
    console.log('Message received in Service Worker:', event.data);
    const { title, body, url, icon } = event.data; // <--- Destructure 'icon' here
    self.registration.showNotification(title, {
        body,
        icon: icon, // <--- Use the icon from the message data
        data: { url }, // Attach the URL for redirection
    });
});

// Listen for notification clicks (this part remains the same)
self.addEventListener('notificationclick', (event) => {
    event.notification.close(); // Close the notification

    // Open or focus the specified URL
    const urlToOpen = event.notification.data.url;

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus(); // Focus the existing tab
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen); // Open a new tab if no matching tab exists
            }
        })
    );
});