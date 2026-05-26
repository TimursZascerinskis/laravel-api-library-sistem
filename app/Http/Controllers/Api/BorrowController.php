<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    public function index()
    {
        return Borrow::with(['book', 'reader'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gramata_id' => 'required|exists:books,id',
            'lasitajs_id' => 'required|exists:readers,id',
            'aiznemsanas_datums' => 'required|date',
            'atdosanas_datums' => 'nullable|date|after_or_equal:aiznemsanas_datums',
        ]);

        return DB::transaction(function () use ($validated) {
            $affected = Book::where('id', $validated['gramata_id'])
                ->where('pieejamie_eksemplari', '>', 0)
                ->decrement('pieejamie_eksemplari');

            if ($affected === 0) {
                return response()->json(['error' => 'Nav pieejamu eksemplāru'], 400);
            }

            return Borrow::create($validated);
        });
    }

    public function show(Borrow $borrow)
    {
        return $borrow->load(['book', 'reader']);
    }

    public function update(Request $request, Borrow $borrow)
    {
        $validated = $request->validate([
            'gramata_id' => 'exists:books,id',
            'lasitajs_id' => 'exists:readers,id',
            'aiznemsanas_datums' => 'date',
            'atdosanas_datums' => 'nullable|date|after_or_equal:aiznemsanas_datums',
        ]);

        return DB::transaction(function () use ($validated, $borrow) {
            if (isset($validated['gramata_id']) && $validated['gramata_id'] != $borrow->gramata_id) {
                $borrow->book()->increment('pieejamie_eksemplari');

                $affected = Book::where('id', $validated['gramata_id'])
                    ->where('pieejamie_eksemplari', '>', 0)
                    ->decrement('pieejamie_eksemplari');

                if ($affected === 0) {
                    throw new \RuntimeException('Nav pieejamu eksemplāru jaunajai grāmatai');
                }
            }

            $borrow->update($validated);

            return $borrow->load(['book', 'reader']);
        });
    }

    public function destroy(Borrow $borrow)
    {
        DB::transaction(function () use ($borrow) {
            $borrow->book()->increment('pieejamie_eksemplari');
            $borrow->delete();
        });

        return response()->noContent();
    }
}
