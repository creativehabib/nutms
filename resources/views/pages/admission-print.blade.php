<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Data - {{ $collegeInfo->college_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* প্রিন্ট করার সময় যেন ব্যাকগ্রাউন্ড সাদা থাকে এবং মার্জিন ঠিক থাকে */
        @media print {
            body { background-color: white !important; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-container { padding: 0 !important; max-width: 100% !important; border: none !important; box-shadow: none !important; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body class="bg-zinc-100 text-zinc-900 antialiased py-10" onload="window.print()">

    <div class="print-container mx-auto max-w-4xl bg-white p-10 shadow-sm border border-zinc-200 rounded-lg">

        <!-- Print Header -->
        <div class="text-center mb-8 border-b border-zinc-200 pb-6">
            <h1 class="text-2xl font-bold uppercase tracking-wide">{{ $collegeInfo->college_name }}</h1>
            <p class="text-sm font-mono mt-1 text-zinc-500">College Code: {{ $collegeInfo->college_code }}</p>
            <p class="mt-4 font-semibold">Admission Summary (Session 2024-25)</p>
        </div>

        <!-- Data Table -->
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-zinc-300">
                    <th class="py-3 px-4 font-bold text-sm text-zinc-700">#</th>
                    <th class="py-3 px-4 font-bold text-sm text-zinc-700">Subject Name</th>
                    <th class="py-3 px-4 font-bold text-sm text-zinc-700">Subject Code</th>
                    <th class="py-3 px-4 font-bold text-sm text-zinc-700 text-right">Admitted Students (Sess 24-25)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $record)
                    <tr class="border-b border-zinc-200">
                        <td class="py-3 px-4 text-sm">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 text-sm font-medium">{{ $record->subject_name }}</td>
                        <td class="py-3 px-4 text-sm font-mono text-zinc-500">{{ $record->subject_id }}</td>
                        <td class="py-3 px-4 text-sm text-right font-bold">{{ $record->sess_24_25_total_admited }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-zinc-50">
                    <td colspan="3" class="py-4 px-4 text-right font-bold text-zinc-800">Total Admitted:</td>
                    <td class="py-4 px-4 text-right font-bold text-lg text-indigo-600">{{ number_format($totalStudents) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer / Signature Area -->
        <div class="mt-16 flex justify-between px-8">
            <div class="text-center">
                <div class="w-40 border-b border-zinc-400 mb-2"></div>
                <p class="text-sm text-zinc-600">Prepared By</p>
            </div>
            <div class="text-center">
                <div class="w-40 border-b border-zinc-400 mb-2"></div>
                <p class="text-sm text-zinc-600">Authority Signature</p>
            </div>
        </div>

        <!-- Print Action Button (Hidden during actual print) -->
        <div class="mt-10 text-center no-print">
            <button onclick="window.print()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium shadow-sm hover:bg-indigo-700 transition">
                Print Document
            </button>
        </div>

    </div>

</body>
</html>
