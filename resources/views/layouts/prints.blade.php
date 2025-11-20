<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.header')

    <title>{{ $title ?? 'My App' }}</title>

  <style>
    @media print {

        body {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 0;
            margin: 0;
            opacity: 1 !important;
            animation: none !important;

        }

        .page {
            width: 100%;
        }

        .print-button {
            display: none !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            background: white !important;
            font-size: 11px !important;
            color: #1f2937 !important;
        }

        table thead {
            background: #f3f4f6 !important;
            text-transform: uppercase !important;
            font-weight: bold !important;
            color: #374151 !important;
        }

        table th {
            border: 1px solid #d1d5db !important;
            padding: 6px !important;
        }

        table td {
            border: 1px solid #d1d5db !important;
            padding: 6px !important;
            word-wrap: break-word !important;
        }

        table tbody tr:nth-child(even) {
            background: #f9fafb !important;
        }

        table tbody tr:nth-child(odd) {
            background: #ffffff !important;
        }

        .header img {
            width: 50px !important;
            height: 50px !important;
        }

        .summary-date,
        .noted {
            font-size: 10px !important;
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
    <button
        onclick="window.print()"
        class="print-button fixed top-4 right-4 bg-blue-600 text-white px-4 py-2 z-50 rounded shadow hover:bg-blue-700">
        Print Report
    </button>


    {{ $slot }}

@livewireScripts
@filamentScripts
@fluxScripts
<script>
    window.onload = () => {
        setTimeout(() => {
            window.print();
        }, 500);
    };
</script>
</body>
</html>
