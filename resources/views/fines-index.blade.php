<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sodi pa lasītājiem</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Sodi pa lasītājiem</h1>
            <div class="flex items-center gap-4">
                <a href="/api/books" class="text-sm text-blue-600 hover:text-blue-800 underline">Grāmatas</a>
                <a href="/api/overdue-borrows" class="text-sm text-blue-600 hover:text-blue-800 underline">&larr; Kavētie aizņēmumi</a>
                <span class="text-sm text-gray-500">{{ count($fines) }} ieraksti</span>
            </div>
        </div>

        @if(count($fines) === 0)
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-500 text-lg">Nav sodāmu lasītāju</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                            <th class="px-4 py-3">Lasītājs</th>
                            <th class="px-4 py-3">E-pasts</th>
                            <th class="px-4 py-3">Kavējumu skaits</th>
                            <th class="px-4 py-3">Kavējuma dienas</th>
                            <th class="px-4 py-3 text-red-600">Kopējais sods</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($fines as $f)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $f->vards }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $f->e_pasts }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $f->kavejumu_skaits }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $f->kavejuma_dienas }}</td>
                                <td class="px-4 py-3 text-sm font-bold text-red-600">{{ number_format($f->kopejais_sods, 2) }} EUR</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="/api/fines/{{ $f->lasitajs_id }}" class="text-blue-600 hover:text-blue-800 underline">Detalizēti</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>
