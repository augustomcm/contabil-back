<?php

namespace Database\Factories;

use App\Models\CreditCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Helpers\Money;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CreditCard>
 */
class CreditCardFactory extends Factory
{
    protected $model = CreditCard::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'limit' => new Money($this->faker->numberBetween(0,1000000)), //0 between 10,000.00
            'description' => $this->faker->creditCardType(),
            'closing_day' => $this->faker->numberBetween(1,31),
            'expiration_day' => $this->faker->numberBetween(1,31)
        ];
    }
}
