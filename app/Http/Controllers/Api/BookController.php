<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('q')) {
            $query->where('nosaukums', 'like', '%' . $request->q . '%');
        }

        $books = $query->get()->map(fn ($book) => $this->addLinks($book));

        if ($request->expectsJson()) {
            return $books;
        }

        $viewData = ['books' => $books];

        if ($request->filled('q') && $request->boolean('explain')) {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'pgsql') {
                $explain = DB::select('EXPLAIN (ANALYZE false, FORMAT TEXT) SELECT * FROM books WHERE nosaukums::text LIKE ?', ['%' . $request->q . '%']);
            } else {
                $explain = DB::select('EXPLAIN QUERY PLAN SELECT * FROM books WHERE nosaukums LIKE ?', ['%' . $request->q . '%']);
            }
            $viewData['explain'] = $explain;
        }

        return view('books.index', $viewData);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nosaukums' => 'required|string|max:255',
            'isbn' => 'required|string|max:20|unique:books,isbn',
            'pieejamie_eksemplari' => 'required|integer|min:0',
        ]);

        return $this->addLinks(Book::create($validated));
    }

    public function show(Book $book)
    {
        return $this->addLinks($book);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'nosaukums' => 'string|max:255',
            'isbn' => 'string|max:20|unique:books,isbn,' . $book->id,
            'pieejamie_eksemplari' => 'integer|min:0',
        ]);

        $book->update($validated);

        return $this->addLinks($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->noContent();
    }

    private function addLinks(Book $book)
    {
        $data = $book->toArray();
        $data['_links'] = [
            'borrows' => ['href' => route('borrows.index', ['gramata_id' => $book->id])],
        ];

        return $data;
    }
}
