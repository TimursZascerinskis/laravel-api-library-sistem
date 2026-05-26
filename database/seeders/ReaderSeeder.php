<?php

namespace Database\Seeders;

use App\Models\Reader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReaderSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Reader::factory(20)->create();
    }
}
