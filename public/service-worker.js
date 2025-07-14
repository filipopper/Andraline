const CACHE_NAME = 'lightcommerce-v1.0.0';
const urlsToCache = [
  '/',
  '/products',
  '/categories',
  '/cart',
  '/assets/css/app.css',
  '/assets/js/app.js',
  '/manifest.json'
];

// Install event - cache resources
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Return cached version or fetch from network
        if (response) {
          return response;
        }
        
        return fetch(event.request).then(response => {
          // Check if valid response
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }
          
          // Clone the response
          const responseToCache = response.clone();
          
          caches.open(CACHE_NAME)
            .then(cache => {
              cache.put(event.request, responseToCache);
            });
          
          return response;
        });
      }
    )
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Background sync for offline cart actions
self.addEventListener('sync', event => {
  if (event.tag === 'cart-sync') {
    event.waitUntil(syncCart());
  }
});

async function syncCart() {
  try {
    // Get pending cart actions from IndexedDB
    const pendingActions = await getPendingCartActions();
    
    for (const action of pendingActions) {
      try {
        await fetch(action.url, {
          method: action.method,
          headers: action.headers,
          body: action.body
        });
        
        // Remove from pending actions
        await removePendingCartAction(action.id);
      } catch (error) {
        console.error('Failed to sync cart action:', error);
      }
    }
  } catch (error) {
    console.error('Background sync failed:', error);
  }
}

// Push notifications
self.addEventListener('push', event => {
  const options = {
    body: event.data ? event.data.text() : 'New update available!',
    icon: '/assets/images/icons/icon-192x192.png',
    badge: '/assets/images/icons/badge-72x72.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: '2'
    },
    actions: [
      {
        action: 'explore',
        title: 'View Products',
        icon: '/assets/images/icons/actions/view-icon.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: '/assets/images/icons/actions/close-icon.png'
      }
    ]
  };
  
  event.waitUntil(
    self.registration.showNotification('LightCommerce', options)
  );
});

// Notification click handling
self.addEventListener('notificationclick', event => {
  console.log('Notification click received.');
  
  event.notification.close();
  
  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/products')
    );
  } else if (event.action === 'close') {
    // Just close the notification
  } else {
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});

// Helper functions for IndexedDB operations
async function getPendingCartActions() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('lightcommerce-offline', 1);
    
    request.onerror = () => reject(request.error);
    
    request.onsuccess = () => {
      const db = request.result;
      const transaction = db.transaction(['pendingActions'], 'readonly');
      const store = transaction.objectStore('pendingActions');
      const getAllRequest = store.getAll();
      
      getAllRequest.onsuccess = () => resolve(getAllRequest.result);
      getAllRequest.onerror = () => reject(getAllRequest.error);
    };
    
    request.onupgradeneeded = () => {
      const db = request.result;
      if (!db.objectStoreNames.contains('pendingActions')) {
        db.createObjectStore('pendingActions', { keyPath: 'id' });
      }
    };
  });
}

async function removePendingCartAction(id) {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('lightcommerce-offline', 1);
    
    request.onerror = () => reject(request.error);
    
    request.onsuccess = () => {
      const db = request.result;
      const transaction = db.transaction(['pendingActions'], 'readwrite');
      const store = transaction.objectStore('pendingActions');
      const deleteRequest = store.delete(id);
      
      deleteRequest.onsuccess = () => resolve();
      deleteRequest.onerror = () => reject(deleteRequest.error);
    };
  });
}