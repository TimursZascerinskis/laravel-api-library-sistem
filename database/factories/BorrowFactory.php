<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Borrow>
 */
class BorrowFactory extends Factory
{
    public function definition(): array
    {
        $aiznemts = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'gramata_id' => Book::factory(),
            'lasitajs_id' => Reader::factory(),
            'aiznemsanas_datums' => $aiznemts->format('Y-m-d'),
            'atdosanas_datums' => fake()->boolean(70)
                ? fake()->dateTimeBetween($aiznemts, '+1 month')->format('Y-m-d')
                : null,
        ];
    }
}
