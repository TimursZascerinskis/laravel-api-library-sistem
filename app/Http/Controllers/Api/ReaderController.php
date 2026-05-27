<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function index()
    {
        return Reader::all()->map(fn ($reader) => $this->addLinks($reader));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vards' => 'required|string|max:255',
            'e_pasts' => 'required|email|max:255|unique:readers,e_pasts',
        ]);

        return $this->addLinks(Reader::create($validated));
    }

    public function show(Reader $reader)
    {
        return $this->addLinks($reader);
    }

    public function update(Request $request, Reader $reader)
    {
        $validated = $request->validate([
            'vards' => 'string|max:255',
            'e_pasts' => 'email|max:255|unique:readers,e_pasts,' . $reader->id,
        ]);

        $reader->update($validated);

        return $this->addLinks($reader);
    }

    public function destroy(Reader $reader)
    {
        $reader->delete();

        return response()->noContent();
    }

    private function addLinks(Reader $reader)
    {
        $data = $reader->toArray();
        $data['_links'] = [
            'borrows' => ['href' => route('borrows.index', ['lasitajs_id' => $reader->id])],
            'fines'   => ['href' => route('fines.calculate', $reader->id)],
        ];

        return $data;
    }
}
