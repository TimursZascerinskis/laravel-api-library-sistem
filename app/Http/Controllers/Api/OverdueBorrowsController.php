<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OverdueBorrowsController extends Controller
{
    public function index()
    {
        $borrows = DB::table('overdue_borrows')->get();

        if (request()->expectsJson()) {
            return $borrows->map(fn ($borrow) => $this->addLinks($borrow));
        }

        return view('overdue-borrows', ['borrows' => $borrows]);
    }

    private function addLinks($borrow)
    {
        $data = (array) $borrow;
        $data['_links'] = [
            'reader'       => ['href' => route('readers.show', $borrow->lasitajs_id)],
            'reader_fines' => ['href' => route('fines.calculate', $borrow->lasitajs_id)],
        ];

        return (object) $data;
    }
}
