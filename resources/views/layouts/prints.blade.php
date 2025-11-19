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
            .page {
                width: 100%;
                max-width: 794px;
                margin: 0 auto;
                padding: 0.5in;
            }

            table {
                width: 100%;
                table-layout: fixed;
                word-wrap: break-word;
                font-size: 10px;
            }

            th, td {
                padding: 4px 6px;
            }

            .stats-grid {
                display: block;
            }

            .stat-card {
                width: 100% !important;
                margin-bottom: 1rem;
            }

            .header img {
                width: 50px !important;
                height: 50px !important;
            }

            .summary-date {
                font-size: 10px;
            }

            .noted {
                font-size: 10px;
            }

            .overflow-x-auto {
                overflow-x: visible !important;
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
