<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function index()
    {
        return Reader::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vards' => 'required|string|max:255',
            'e_pasts' => 'required|email|max:255|unique:readers,e_pasts',
        ]);

        return Reader::create($validated);
    }

    public function show(Reader $reader)
    {
        return $reader;
    }

    public function update(Request $request, Reader $reader)
    {
        $validated = $request->validate([
            'vards' => 'string|max:255',
            'e_pasts' => 'email|max:255|unique:readers,e_pasts,' . $reader->id,
        ]);

        $reader->update($validated);

        return $reader;
    }

    public function destroy(Reader $reader)
    {
        $reader->delete();

        return response()->noContent();
    }
}
