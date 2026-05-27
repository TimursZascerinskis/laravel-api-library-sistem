<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ReaderController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\ZurnalController;
use App\Http\Controllers\Api\OverdueBorrowsController;
use App\Http\Controllers\Api\FineController;
use Illuminate\Support\Facades\Route;

Route::get('overdue-borrows', [OverdueBorrowsController::class, 'index']);
Route::get('fines/{reader}', [FineController::class, 'calculate']);

Route::apiResource('books', BookController::class);
Route::apiResource('readers', ReaderController::class);
Route::apiResource('borrows', BorrowController::class);
Route::get('zurnals', [ZurnalController::class, 'index']);
