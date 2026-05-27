<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kavētie aizņēmumi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Kavētie aizņēmumi</h1>
            <span class="text-sm text-gray-500">{{ count($borrows) }} ieraksti</span>
        </div>

        @if(count($borrows) === 0)
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-500 text-lg">Nav kavētu aizņēmumu</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Grāmata</th>
                            <th class="px-4 py-3">ISBN</th>
                            <th class="px-4 py-3">Lasītājs</th>
                            <th class="px-4 py-3">E-pasts</th>
                            <th class="px-4 py-3">Aizņemts</th>
                            <th class="px-4 py-3">Plānotā atdošana</th>
                            <th class="px-4 py-3 text-red-600">Kavējums (dienas)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($borrows as $b)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $b->borrow_id }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $b->gramatas_nosaukums }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $b->isbn }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $b->lasitaja_vards }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $b->lasitaja_e_pasts }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $b->aiznemsanas_datums }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $b->planotais_atdosanas_datums }}</td>
                                <td class="px-4 py-3 text-sm font-bold {{ $b->kavejuma_dienas > 60 ? 'text-red-600' : ($b->kavejuma_dienas > 30 ? 'text-orange-500' : 'text-yellow-600') }}">
                                    {{ $b->kavejuma_dienas }}
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
