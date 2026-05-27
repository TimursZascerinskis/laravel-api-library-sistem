<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grāmatu saraksts</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Grāmatu saraksts</h1>
            <div class="flex items-center gap-4">
                <a href="/api/overdue-borrows" class="text-sm text-blue-600 hover:text-blue-800 underline">Kavētie aizņēmumi</a>
                <a href="/api/fines" class="text-sm text-blue-600 hover:text-blue-800 underline">Sodi</a>
                <span class="text-sm text-gray-500">{{ count($books) }} grāmatas</span>
            </div>
        </div>

        <div class="mb-6">
            <form method="GET" action="/api/books" class="flex gap-3">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Meklēt pēc nosaukuma..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                >
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">Meklēt</button>
                @if(request('q'))
                    <a href="/api/books" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">Notīrīt</a>
                @endif
            </form>
        </div>

        @if(count($books) === 0)
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-500 text-lg">Nav atrasta neviena grāmata</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Nosaukums</th>
                            <th class="px-4 py-3">ISBN</th>
                            <th class="px-4 py-3">Pieejamie eksemplāri</th>
                            <th class="px-4 py-3">Izveidots</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($books as $book)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $book['id'] }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $book['nosaukums'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $book['isbn'] }}</td>
                                <td class="px-4 py-3 text-sm {{ $book['pieejamie_eksemplari'] > 0 ? 'text-green-600 font-medium' : 'text-red-500' }}">
                                    {{ $book['pieejamie_eksemplari'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $book['created_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(isset($explain))
            <div class="mt-6 bg-gray-900 text-green-400 rounded-lg p-4 font-mono text-sm">
                <p class="text-gray-400 mb-2">-- EXPLAIN QUERY PLAN --</p>
                @foreach($explain as $row)
                    <p>{{ $row->detail ?? json_encode($row) }}</p>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
