<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ReaderController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\ZurnalController;
use Illuminate\Support\Facades\Route;

Route::apiResource('books', BookController::class);
Route::apiResource('readers', ReaderController::class);
Route::apiResource('borrows', BorrowController::class);
Route::get('zurnals', [ZurnalController::class, 'index']);
