<?php

namespace Tests\Unit;

use App\Models\AccountDefault;
use App\Models\Entry;
use App\Helpers\Money;
use Tests\TestCase;

class EntryTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_pay_entry()
    {
        $entry = Entry::factory()->make([
            'value' => new Money(10000)
        ]);

        $account = AccountDefault::factory(['balance' => new Money(10000)])->make();

        $date = new \DateTimeImmutable('2022-02-12');
        $entry->pay($account, $date);

        $this->assertEquals($entry->getAccount(), $account);
        $this->assertEquals($account->getBalance(), new Money(0));
        $this->assertTrue($entry->isPaid());
        $this->assertEquals($date, $entry->paid_at);
    }

    public function test_cancel_payment_entry()
    {
        $entry = Entry::factory()->make([
            'value' => new Money(10000)
        ]);

        $account = AccountDefault::factory(['balance' => new Money(10000)])->make();
        $date = new \DateTimeImmutable('2022-02-12');
        $entry->pay($account, $date);

        $entry->cancelPayment();

        $this->assertNull($entry->getAccount());
        $this->assertEquals($account->getBalance(), new Money(10000));
        $this->assertFalse($entry->isPaid());
        $this->assertNull($entry->paid_at);
    }

    public function test_duplicate_payment_entry()
    {
        $entry = Entry::factory()->make([
            'value' => new Money(10000)
        ]);

        $account = AccountDefault::factory(['balance' => new Money(10000)])->make();

        $date = new \DateTimeImmutable('2022-02-12');
        $entry->pay($account, $date);
        $entry->pay($account, $date);

        $this->assertEquals($account->getBalance(), new Money(0));
    }

    public function test_duplicate_cancel_payment_entry()
    {
        $entry = Entry::factory()->make([
            'value' => new Money(10000)
        ]);

        $account = AccountDefault::factory(['balance' => new Money(10000)])->make();
        $date = new \DateTimeImmutable('2022-02-12');
        $entry->pay($account, $date);

        $entry->cancelPayment();
        $entry->cancelPayment();

        $this->assertEquals($account->getBalance(), new Money(10000));
    }
}
