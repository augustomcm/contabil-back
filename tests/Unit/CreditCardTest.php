<?php

namespace Tests\Unit;

use App\Models\CreditCard;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Helpers\Money;
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
            'expiration_day' => $this->faker->numberBetween(1,31),
            'limit' => new Money(10000) // 100.00
        ]);

        $creditCard->save();

        $this->assertNotNull($creditCard->id);
        $this->assertEquals($creditCard->id, Invoice::first()->id);
    }

    public function test_if_debit_value()
    {
        $creditCard = CreditCard::factory()->create([
            'limit' => new Money(10000) // 100.00
        ]);

        $creditCard->debit(new Money(6000));

        $expected = new Money(4000);
        $this->assertTrue($expected->equals($creditCard->limit));
    }

    public function test_if_debit_value_greater_than_limit()
    {
        $this->expectException(\InvalidArgumentException::class);

        $creditCard = CreditCard::factory()->create([
            'limit' => new Money(10000) // 100.00
        ]);

        $creditCard->debit(new Money(10001)); // 100.01
    }

    public function test_if_refunding_value()
    {
        $creditCard = CreditCard::factory()->create([
            'limit' => new Money(10000) // 100.00
        ]);

        $creditCard->refunding(new Money(6000));

        $expected = new Money(16000);
        $this->assertTrue($expected->equals($creditCard->limit));
    }
}
