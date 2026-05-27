<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OverdueBorrowsController extends Controller
{
    public function index()
    {
        return DB::table('overdue_borrows')->get();
    }
}
