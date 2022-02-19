<?php

namespace Tests\Unit;

use App\Helpers\Money;
use App\Models\AccountDefault;
use App\Models\CreditCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class AccountDefaultTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_if_debit_value()
    {
        $limitValue = $this->faker->numberBetween(1, 10000); // 0.01 between 100.00
        $valueDebit = $this->faker->numberBetween(1, $limitValue); // 0.01 between limitValue
        $account = AccountDefault::factory()->create([
            'balance' => new Money($limitValue)
        ]);

        $account->debit(new Money($valueDebit));

        $expected = new Money($limitValue-$valueDebit);
        $this->assertTrue($expected->equals($account->getBalance()));
    }

    public function test_if_debit_value_greater_than_balance()
    {
        $this->expectException(\InvalidArgumentException::class);

        $limitValue = $this->faker->numberBetween(1, 10000);
        $account = AccountDefault::factory()->create([
            'balance' => new Money($limitValue)
        ]);

        $account->debit(new Money($limitValue+1));
    }

    public function test_if_deposit_value()
    {
        $limitValue = $this->faker->numberBetween(1, 10000); // 0.01 between 100.00
        $valueDebit = $this->faker->numberBetween(1, $limitValue); // 0.01 between limitValue
        $account = AccountDefault::factory()->create([
            'balance' => new Money($limitValue)
        ]);

        $account->deposit(new Money($valueDebit));

        $expected = new Money($limitValue+$valueDebit);
        $this->assertTrue($expected->equals($account->getBalance()));
    }
}
