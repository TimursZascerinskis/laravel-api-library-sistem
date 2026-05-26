<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ZurnalController extends Controller
{
    public function index()
    {
        return DB::table('zurnals')
            ->join('books', 'zurnals.gramata_id', '=', 'books.id')
            ->select('zurnals.*', 'books.nosaukums as gramatas_nosaukums')
            ->latest('zurnals.created_at')
            ->get();
    }
}
