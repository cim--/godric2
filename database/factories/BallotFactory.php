<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ballot>
 */
class BallotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(5),
            'start' => $this->faker->dateTimeThisYear(),
            'end' => $this->faker->dateTimeThisYear(),
            'votersonly' => false,
        ];
    }
}
