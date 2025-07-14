<?php

namespace Controllers;

use Core\Controller;

class PwaController extends Controller
{
    public function manifest()
    {
        header('Content-Type: application/json');
        
        $manifest = [
            'name' => 'Lightweight eCommerce',
            'short_name' => 'eCommerce',
            'description' => 'Innovative eCommerce Platform',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#3b82f6',
            'orientation' => 'portrait-primary',
            'icons' => [
                [
                    'src' => '/public/icons/icon-72x72.png',
                    'sizes' => '72x72',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/public/icons/icon-96x96.png',
                    'sizes' => '96x96',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/public/icons/icon-128x128.png',
                    'sizes' => '128x128',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/public/icons/icon-144x144.png',
                    'sizes' => '144x144',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/public/icons/icon-152x152.png',
                    'sizes' => '152x152',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/public/icons/icon-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/public/icons/icon-384x384.png',
                    'sizes' => '384x384',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/public/icons/icon-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ]
            ],
            'categories' => ['shopping', 'business'],
            'lang' => 'en',
            'dir' => 'ltr'
        ];
        
        echo json_encode($manifest);
    }

    public function serviceWorker()
    {
        header('Content-Type: application/javascript');
        
        $sw = "
        const CACHE_NAME = 'ecommerce-v1';
        const urlsToCache = [
            '/',
            '/public/css/app.css',
            '/public/js/app.js',
            'https://cdn.tailwindcss.com',
            'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js'
        ];

        self.addEventListener('install', function(event) {
            event.waitUntil(
                caches.open(CACHE_NAME)
                    .then(function(cache) {
                        return cache.addAll(urlsToCache);
                    })
            );
        });

        self.addEventListener('fetch', function(event) {
            event.respondWith(
                caches.match(event.request)
                    .then(function(response) {
                        // Return cached version or fetch from network
                        return response || fetch(event.request);
                    })
            );
        });

        self.addEventListener('activate', function(event) {
            event.waitUntil(
                caches.keys().then(function(cacheNames) {
                    return Promise.all(
                        cacheNames.map(function(cacheName) {
                            if (cacheName !== CACHE_NAME) {
                                return caches.delete(cacheName);
                            }
                        })
                    );
                })
            );
        });

        // Background sync for offline functionality
        self.addEventListener('sync', function(event) {
            if (event.tag === 'background-sync') {
                event.waitUntil(doBackgroundSync());
            }
        });

        function doBackgroundSync() {
            // Handle background sync for offline actions
            return Promise.resolve();
        }
        ";
        
        echo $sw;
    }
}