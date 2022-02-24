<?php

namespace Tests\Unit;

use App\Helpers\Money;
use App\Models\CreditCard;
use App\Models\Entry;
use App\Models\EntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntryServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
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
