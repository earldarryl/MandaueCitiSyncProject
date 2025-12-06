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
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

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
            table-layout: auto !important;
            background: white !important;
            font-size: 11px !important;
            color: #1f2937 !important;
        }

        .grievance-remark-td {
            width: 30% !important;
            max-width: 30% !important;
            white-space: normal !important;
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

        .status-footer {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 50px;
            margin-left: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            width: fit-content;
            color: #1f2937;
        }

        .status-footer .footer-row {
            display: flex;
            align-items: flex-end;
            gap: 6px;
        }

        .status-footer .footer-label {
            font-weight: 600;
            color: #374151;
        }

        .status-footer .footer-value {
            font-weight: 600;
            font-size: 15px;
            border-bottom: 1.8px solid #374151;
            padding-bottom: 2px;
            letter-spacing: 0.3px;
            color: #111827;
        }

        .status-footer .footer-subtext {
            text-align: center;
            font-size: 12px;
            color: #6B7280;
            font-weight: 500;
            margin-top: 2px;
            letter-spacing: 0.2px;
        }
    }

    body {
        font-family: 'Inter', sans-serif;
        padding: 2rem;
    }

    .status-footer {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 50px;
            margin-left: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            width: fit-content;
            color: #1f2937;
        }

        .status-footer .footer-row {
            display: flex;
            align-items: flex-end;
            gap: 6px;
        }

        .status-footer .footer-label {
            font-weight: 600;
            color: #374151;
        }

        .status-footer .footer-value {
            font-weight: 600;
            font-size: 15px;
            border-bottom: 1.8px solid #374151;
            padding-bottom: 2px;
            letter-spacing: 0.3px;
            color: #111827;
        }

        .status-footer .footer-subtext {
            text-align: center;
            font-size: 12px;
            color: #6B7280;
            font-weight: 500;
            margin-top: 2px;
            letter-spacing: 0.2px;
        }
</style>

    @livewireStyles
    @filamentStyles
    @fluxAppearance
</head>
<body class="font-sans antialiased animate-fadeIn dark:bg-white">
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
