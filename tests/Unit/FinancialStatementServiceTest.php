<?php

namespace Tests\Unit;

use App\Helpers\Money;
use App\Models\Entry;
use Tests\TestCase;
use App\Models\FinancialStatementService;

class FinancialStatementServiceTest extends TestCase
{
    public function test_calculate_total_income()
    {
        $expenses = Entry::factory(2)
            ->make([
                'value' => new Money(1000)
            ]);

        $incomes = Entry::factory(2)
            ->income()
            ->make([
                'value' => new Money(1000)
            ]);

        $entries = $expenses->concat($incomes);

        $service = new FinancialStatementService();

        $total = $service->calculateTotalIncome($entries);

        $this->assertEquals(new Money(2000), $total);
    }

    public function test_calculate_total_expense()
    {
        $expenses = Entry::factory(2)
            ->make([
                'value' => new Money(1000)
            ]);

        $incomes = Entry::factory(2)
            ->income()
            ->make([
                'value' => new Money(1000)
            ]);

        $entries = $expenses->concat($incomes);


        $service = new FinancialStatementService();

        $total = $service->calculateTotalExpense($entries);

        $this->assertEquals(new Money(2000), $total);
    }

    public function test_calculate_balance()
    {
        $expenses = Entry::factory(2)
            ->make([
                'value' => new Money(1000)
            ]);

        $incomes = Entry::factory(2)
            ->income()
            ->make([
                'value' => new Money(1000)
            ]);

        $entries = $expenses->concat($incomes);

        $service = new FinancialStatementService();

        $total = $service->calculateBalance($entries);

        $this->assertEquals(new Money(0), $total);
    }

}
