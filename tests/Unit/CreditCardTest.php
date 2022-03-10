<?php

namespace Tests\Unit;

use App\Models\CreditCard;
use App\Models\Entry;
use App\Models\Invoice;
use App\Models\User;
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
            'limit' => new Money(10000), // 100.00
        ]);
        $creditCard->owner()->associate(User::factory()->create());

        $creditCard->save();

        $this->assertNotNull($creditCard->id);
        $this->assertEquals($creditCard->id, Invoice::first()->id);
    }

    public function test_if_debit_value()
    {
        $limitValue = $this->faker->numberBetween(1, 10000); // 0.01 between 100.00
        $valueDebit = $this->faker->numberBetween(1, $limitValue); // 0.01 between limitValue
        $creditCard = CreditCard::factory()->create([
            'limit' => new Money($limitValue) // 100.00
        ]);

        $creditCard->debit(new Money($valueDebit));

        $expected = new Money($limitValue-$valueDebit);
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

    public function test_add_entry_in_current_invoice()
    {
        $limitValue = $this->faker->numberBetween(1, 10000); // 0.01 between 100.00
        $entryValue = $this->faker->numberBetween(1, $limitValue); // 0.01 between limitValue

        $currentInvoice = CreditCard::factory()->create([
            'limit' => new Money($limitValue)
        ])->getCurrentInvoice();

        $entry = Entry::factory()->create([
            'value' => new Money($entryValue)
        ]);

        $currentInvoice->addEntry($entry);

        $limitExpected = new Money($limitValue - $entryValue);
        $this->assertTrue($limitExpected->equals($currentInvoice->getCreditCard()->limit));
        $this->assertCount(1, $currentInvoice->entries);
    }

    public function test_refunding_entry_in_current_invoice()
    {
        $limitValue = $this->faker->numberBetween(1, 10000); // 0.01 between 100.00
        $entryValue = $this->faker->numberBetween(1, 10000); // 0.01 between limitValue

        $currentInvoice = CreditCard::factory()->create([
            'limit' => new Money($limitValue)
        ])->getCurrentInvoice();

        $entry = Entry::factory()->create([
            'value' => new Money($entryValue)
        ]);
        $currentInvoice->entries()->attach($entry);

        $currentInvoice->removeEntry($entry);

        $limitExpected = new Money($limitValue + $entryValue);
        $this->assertTrue($limitExpected->equals($currentInvoice->getCreditCard()->limit));
        $this->assertCount(0, $currentInvoice->entries);
    }

    public function test_close_current_invoice()
    {
        $creditCard = CreditCard::factory()->create([
            'closing_day' => now()->subDay()->day
        ]);

        $creditCard->closeCurrentInvoice();

        $this->assertTrue($creditCard->getCurrentInvoice()->isClosed());
    }

    public function test_pay_closed_invoice()
    {
        $creditCard = CreditCard::factory()->create([
            'closing_day' => now()->subDay()->day
        ]);

        $creditCard->closeCurrentInvoice();

        $this->assertTrue($creditCard->getCurrentInvoice()->isClosed());
    }
}
