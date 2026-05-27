<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Reader;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OverdueBorrowSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $book = Book::first();
        $reader = Reader::first();

        if (!$book || !$reader) {
            $this->command->warn('Vispirms izpildiet BookSeeder un ReaderSeeder');
            return;
        }

        Borrow::create([
            'gramata_id' => $book->id,
            'lasitajs_id' => $reader->id,
            'aiznemsanas_datums' => Carbon::now()->subDays(30)->format('Y-m-d'),
            'atdosanas_datums' => null,
        ]);

        $this->command->info('Pievienots kavēts aizņēmums (30 dienas pagātnē, nav atdots)');
    }
}
