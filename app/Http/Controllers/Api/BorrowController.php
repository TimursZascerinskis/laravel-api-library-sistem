<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrow::with(['book', 'reader']);

        if ($request->filled('gramata_id')) {
            $query->where('gramata_id', $request->gramata_id);
        }

        if ($request->filled('lasitajs_id')) {
            $query->where('lasitajs_id', $request->lasitajs_id);
        }

        return $query->get()->map(fn ($borrow) => $this->addLinks($borrow));
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

            return $this->addLinks(Borrow::create($validated)->load(['book', 'reader']));
        });
    }

    public function show(Borrow $borrow)
    {
        return $this->addLinks($borrow->load(['book', 'reader']));
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

            return $this->addLinks($borrow->load(['book', 'reader']));
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

    private function addLinks(Borrow $borrow)
    {
        $data = $borrow->toArray();
        $data['_links'] = [
            'book'   => ['href' => route('books.show', $borrow->gramata_id)],
            'reader' => ['href' => route('readers.show', $borrow->lasitajs_id)],
        ];

        return $data;
    }
}
