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
            'description' => $this->faker->text,
            'value' => new Money(10000),
            'payment_type' => EntryPaymentType::DEFAULT,
            'owner_id' => User::factory(),
            'type' => EntryType::EXPENSE
        ];
    }

    public function withPayment()
    {
        return $this->state([])->afterMaking(function(Entry $entry) {
            $entry->pay(
              AccountDefault::factory()->create(),
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
