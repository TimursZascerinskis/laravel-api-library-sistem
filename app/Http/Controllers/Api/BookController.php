<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return Book::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nosaukums' => 'required|string|max:255',
            'isbn' => 'required|string|max:20|unique:books,isbn',
            'pieejamie_eksemplari' => 'required|integer|min:0',
        ]);

        return Book::create($validated);
    }

    public function show(Book $book)
    {
        return $book;
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'nosaukums' => 'string|max:255',
            'isbn' => 'string|max:20|unique:books,isbn,' . $book->id,
            'pieejamie_eksemplari' => 'integer|min:0',
        ]);

        $book->update($validated);

        return $book;
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->noContent();
    }
}
