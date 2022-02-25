<?php

namespace Tests\Unit;

use App\Models\AccountDefault;
use App\Models\Entry;
use App\Helpers\Money;
use App\Models\EntryType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider dataProviderCreate
     */
    public function test_pay_entry($type, $value, $expected)
    {
        $entry = Entry::factory()->make([
            'value' => new Money($value),
            'type' => $type
        ]);

        $account = AccountDefault::factory(['balance' => new Money($value)])->make();

        $date = new \DateTimeImmutable('2022-02-12');
        $entry->pay($account, $date);

        $this->assertEquals($entry->getAccount(), $account);
        $this->assertEquals($account->getBalance(), new Money($expected));
        $this->assertTrue($entry->isPaid());
        $this->assertEquals($date, $entry->paid_at);
    }

    /**
     * @dataProvider dataProviderRemove
     */
    public function test_cancel_payment_entry($type, $value, $expected)
    {
        $entry = Entry::factory()->make([
            'value' => new Money($value),
            'type' => $type
        ]);

        $account = AccountDefault::factory(['balance' => new Money($value)])->make();
        $date = new \DateTimeImmutable('2022-02-12');
        $entry->pay($account, $date);

        $entry->cancelPayment();

        $this->assertNull($entry->getAccount());
        $this->assertEquals($account->getBalance(), new Money($expected));
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

    public function dataProviderCreate()
    {
        return [
            [EntryType::EXPENSE, 1000, 0],
            [EntryType::INCOME, 1000, 2000],
        ];
    }

    public function dataProviderRemove()
    {
        return [
            [EntryType::EXPENSE, 1000, 1000],
            [EntryType::INCOME, 1000, 1000],
        ];
    }
}
