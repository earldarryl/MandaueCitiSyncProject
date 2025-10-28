<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Not Found</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center min-h-screen font-sans">
    <div class="text-center px-4">
        <h1 class="text-[8rem] font-extrabold text-gray-900 mb-4 md:text-[10rem]">404</h1>
        <p class="text-2xl md:text-3xl text-gray-700 mb-6">Oops! The page you’re looking for doesn’t exist.</p>
        <p class="text-gray-500 mb-6">It might have been moved or deleted.</p>
    </div>

    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none rotate-180 pointer-events-none">
        <svg class="relative block w-full h-32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,0 C150,100 350,0 500,50 L500,00 L0,0 Z" fill="#f3f4f6"></path>
        </svg>
    </div>
</body>
</html>
