<?php

namespace Tests\Unit;

use App\Helpers\Money;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\Entry;
use App\Models\EntryService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntryServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_expense_entry_credit_card()
    {
        $owner = User::factory()->create();
        $category = Category::factory()->create(['owner_id' => $owner->id]);
        $creditCard = CreditCard::factory(['owner_id' => $owner->id])
            ->create([ 'limit' => new Money(1000) ]);
        $date = new \DateTime('today');

        $service = new EntryService();

        $entry = $service->createExpenseEntry(
            $date, $this->faker->text, new Money(1000), $owner, $category, $creditCard);

        $currentInvoice = $creditCard->getCurrentInvoice();
        $this->assertCount(1, $currentInvoice->entries()->get());
        $this->assertTrue($entry->isCreditCardType());
    }

    public function test_delete_entry()
    {
        $entry = Entry::factory()->create();
        $service = new EntryService();

        $service->deleteEntry($entry);

        $this->assertCount(0, Entry::all());
    }

    public function test_delete_entry_with_payment()
    {
        $entry = Entry::factory()->withPayment()->create();
        $service = new EntryService();

        $service->deleteEntry($entry);

        $this->assertCount(0, Entry::all());
        $this->assertNull($entry->getAccount());
    }

    public function test_delete_entry_of_credit_cart()
    {
        $currentInvoice = CreditCard::factory()
            ->withEntry()
            ->create()
            ->getCurrentInvoice();

        $entry = $currentInvoice->entries->first();
        $service = new EntryService();

        $service->deleteEntry($entry);

        $this->assertCount(0, $currentInvoice->entries()->get());
    }
}
