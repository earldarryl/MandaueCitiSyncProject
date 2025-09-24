<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Not Found</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-mc_primary_color">404</h1>
        <p class="text-xl mt-4 text-gray-700">Oops! Page not found.</p>
        <a href="{{ url('/') }}" class="mt-6 inline-block px-6 py-2 bg-gray-900 text-white rounded hover:bg-violet-700">
            Go back home
        </a>
    </div>
</body>
</html>
