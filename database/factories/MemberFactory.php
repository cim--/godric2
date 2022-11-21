<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $membertype = $this->faker->randomElement([
                "Standard",
                "Standard",
                "Standard",
                "Standard Free",
                "Student"
        ]);
        return [
            // "membership" => must be specified!
            "firstname" => $this->faker->firstName(),
            "lastname" => $this->faker->lastName(),
            "email" => $this->faker->safeEmail(),
            "mobile" => $this->faker->randomElement([
                "",
                "07".$this->faker->randomNumber(9, true),
                "01".$this->faker->randomNumber(9, true)
            ]),
            "department" => $this->faker->randomElement([
                "Chemistry",
                "Chemistry",
                "Chemistry",
                "Library",
                "Library",
                "Philosophy",
                "Philosophy",
                "Philosophy",
                "Philosophy",
                "Philosophy",
                "Philosophy",
                "Philosophy",
                "Strategic Paperwork"
            ]),
            "jobtype" => $this->faker->randomElement([
                "Academic",
                "Academic",
                "Academic",
                "Academic-related",
                "Postgraduate"
            ]),
            "membertype" => $membertype,
            "voter" => ($membertype != "Student"),
            "created_at" => $this->faker->dateTimeBetween("-6 months", "-1 day")
        ];
    }
}
