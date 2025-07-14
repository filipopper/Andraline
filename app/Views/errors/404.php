<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - LightCommerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <div class="text-6xl font-bold text-primary-600 mb-4">404</div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Page Not Found</h1>
            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Sorry, the page you're looking for doesn't exist or has been moved.
            </p>
        </div>
        
        <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
            <a href="/" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                Go Home
            </a>
            <a href="/products" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-lg transition-colors">
                Browse Products
            </a>
        </div>
        
        <div class="mt-12">
            <div class="flex items-center justify-center space-x-2 text-gray-500">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">L</span>
                </div>
                <span class="text-xl font-bold text-gray-700">LightCommerce</span>
            </div>
        </div>
    </div>
</body>
</html>