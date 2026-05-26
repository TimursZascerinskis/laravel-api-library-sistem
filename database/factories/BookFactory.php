<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected static ?string $isbn;

    public function definition(): array
    {
        return [
            'nosaukums' => fake()->sentence(rand(2, 5)),
            'isbn' => static::$isbn ?? fake()->unique()->isbn13(),
            'pieejamie_eksemplari' => fake()->numberBetween(0, 10),
        ];
    }
}
