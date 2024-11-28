<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Member;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start = Carbon::parse(
            $this->faker->dateTimeBetween('-12 months', '-2 months')
        );
        return [
            'name' => 'Ballot ' . $start->format('F Y'),
            'description' => $this->faker->paragraph(),
            'start' => $start,
            'end' => $start->copy()->addMonths(1),
            'target' => 50,
            'calctarget' => ceil(Member::voter()->count() / 2),
            'votersonly' => true,
        ];
    }

    public function current()
    {
        return $this->state(function (array $attributes) {
            $start = Carbon::parse('-1 week');
            return [
                'name' => 'Ballot ' . $start->format('F Y'),
                'start' => $start,
                'end' => $start->copy()->addMonths(1),
            ];
        });
    }
}
