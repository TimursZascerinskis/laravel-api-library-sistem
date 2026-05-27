<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('q')) {
            $query->where('nosaukums', 'like', '%' . $request->q . '%');
        }

        return $query->get()->map(fn ($book) => $this->addLinks($book));
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
