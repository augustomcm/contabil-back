<?php

namespace Database\Factories;

use App\Models\CreditCard;
use App\Models\Entry;
use App\Models\User;
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
            'expiration_day' => $this->faker->numberBetween(1,31),
            'owner_id' => User::factory()
        ];
    }

    public function withClosedInvoice()
    {
        return $this->state([])->afterCreating(function(CreditCard $creditCard) {
            $entry = Entry::factory()
                ->creditCardType()
                ->create([
                    'owner_id' => $creditCard->owner_id,
                    'value' => $creditCard->limit
                ]);

            $currentInvoice = $creditCard->getCurrentInvoice();
            $currentInvoice->final_date = now()->setDay($creditCard->closing_day)->startOfDay();
            $currentInvoice->save();

            $currentInvoice->addEntry($entry);

            $creditCard->closeCurrentInvoice();
        });
    }

    public function withEntry()
    {
        return $this->state([])->afterCreating(function(CreditCard $creditCard) {
            $entry = Entry::factory()
                ->creditCardType()
                ->create([
                    'owner_id' => $creditCard->owner_id,
                    'value' => $creditCard->limit
                ]);

            $currentInvoice = $creditCard->getCurrentInvoice();
            $currentInvoice->addEntry($entry);
        });
    }
}
