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
            return $borrows;
        }

        return view('overdue-borrows', ['borrows' => $borrows]);
    }
}
