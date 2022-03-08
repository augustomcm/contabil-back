<?php

namespace Database\Factories;

use App\Models\EntryType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->word,
            'color' => $this->faker->hexColor,
            'type' => EntryType::EXPENSE,
            'owner_id' => User::factory()
        ];
    }

    public function income()
    {
        return $this->state([
            'type' => EntryType::INCOME
        ]);
    }
}
