<?php

namespace Database\Factories;

use App\Helpers\Money;
use App\Models\Account;
use App\Models\AccountDefault;
use App\Models\Entry;
use App\Models\EntryPaymentType;
use App\Models\EntryType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entry>
 */
class EntryFactory extends Factory
{
    protected $model = Entry::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->name,
            'value' => new Money($this->faker->randomNumber(5, false)),
            'payment_type' => EntryPaymentType::DEFAULT,
            'owner_id' => User::factory(),
            'type' => EntryType::EXPENSE
        ];
    }

    public function income()
    {
        return $this->state([
            'type' => EntryType::INCOME
        ]);
    }

    public function withPayment()
    {
        return $this->state([])->afterMaking(function(Entry $entry) {
            $entry->pay(
              AccountDefault::factory()->create([
                  'balance' => $entry->getValue()
              ]),
              now()
            );
        });
    }

    public function creditCardType()
    {
        return $this->state([
            'payment_type' => EntryPaymentType::CREDIT_CARD
        ]);
    }

}
