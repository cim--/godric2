<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 'ballot_id',
            'option' => ucfirst($this->faker->word()),
            'votes' => $this->faker->numberBetween(3, 30),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
