<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Soda aprēķins — {{ $data['lasitaja_vards'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-100 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">Naudas soda aprēķins</h1>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Lasītājs</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $data['lasitaja_vards'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">E-pasts</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $data['lasitaja_e_pasts'] }}</p>
                    </div>
                </div>

                <hr class="border-gray-200">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Kavēto aizņēmumu skaits</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $data['kavejumu_skaits'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Kopējās kavējuma dienas</p>
                        <p class="text-2xl font-bold {{ $data['kavejuma_dienas'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $data['kavejuma_dienas'] }}
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Soda likme</p>
                    <p class="text-lg font-semibold text-gray-700">{{ number_format($data['sods_par_dienu'], 2) }} EUR / dienā</p>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-600 font-medium">Kopējais naudas sods</p>
                    <p class="text-3xl font-bold text-red-700">{{ number_format($data['kopejais_sods'], 2) }} EUR</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
