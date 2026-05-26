<?php

namespace Database\Factories;

use App\Models\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reader>
 */
class ReaderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vards' => fake()->name(),
            'e_pasts' => fake()->unique()->safeEmail(),
        ];
    }
}
