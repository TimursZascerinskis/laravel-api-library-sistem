<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Reader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BorrowSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $books = Book::all();
        $readers = Reader::all();

        Borrow::factory(30)
            ->recycle($books)
            ->recycle($readers)
            ->create();
    }
}
