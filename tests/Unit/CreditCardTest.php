<?php

namespace Tests\Unit;

use App\Models\CreditCard;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreditCardTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_create_credit_card()
    {
        $creditCard = new CreditCard([
            'description' => $this->faker->creditCardType(),
            'closing_day' => $this->faker->numberBetween(1,31),
            'expiration_day' => $this->faker->numberBetween(1,31)
        ]);

        $creditCard->save();

        $this->assertNotNull($creditCard->id);
        $this->assertEquals($creditCard->id, Invoice::first()->id);
    }
}
