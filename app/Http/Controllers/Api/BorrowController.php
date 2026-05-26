<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Book;
use Illuminate\Http\Request;

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

        $book = Book::findOrFail($validated['gramata_id']);

        if ($book->pieejamie_eksemplari <= 0) {
            return response()->json(['error' => 'Nav pieejamu eksemplāru'], 400);
        }

        $book->decrement('pieejamie_eksemplari');

        return Borrow::create($validated);
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

        $borrow->update($validated);

        return $borrow->load(['book', 'reader']);
    }

    public function destroy(Borrow $borrow)
    {
        $borrow->book()->increment('pieejamie_eksemplari');
        $borrow->delete();

        return response()->noContent();
    }
}
