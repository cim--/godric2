<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Action>
 */
class ActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 'member_id'
            // 'campaign_id'
            'action' => $this->faker->randomElement([
                'yes',
                'yes',
                'yes',
                'yes',
                'wait',
                'help',
                'no',
            ]),
        ];
    }
}
