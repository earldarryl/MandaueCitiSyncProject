<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Shared head content --}}
    @include('partials.header')

    <title>{{ $title ?? 'My App' }}</title>

  <style>
        @media print {
            @page {
                size: A4;
                margin: 1in;
            }

            body {
                background: white !important;
                color: black !important;
            }

            .no-print {
                display: none !important;
            }

            .bg-white,
            .bg-gray-100,
            .dark\:bg-gray-800,
            .dark\:bg-zinc-900 {
                background: white !important;
                color: black !important;
            }

            .shadow,
            .shadow-sm,
            .shadow-md,
            .shadow-lg {
                box-shadow: none !important;
            }

            * {
                opacity: 1 !important;
            }

            img {
                opacity: 1 !important;
            }

            [x-cloak], [x-show], [x-data], .fixed, .absolute {
                display: none !important;
            }
        }

        body {
            font-family: 'Inter', sans-serif;
            padding: 2rem;
        }
    </style>

    @livewireStyles
    @filamentStyles
    @fluxAppearance
</head>
<body class="font-sans antialiased animate-fadeIn">


    {{ $slot }}

@livewireScripts
@filamentScripts
@fluxScripts
<script>
    window.onload = () => window.print();
</script>
</body>
</html>
