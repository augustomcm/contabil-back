<?php

namespace App\Models;

use App\Helpers\Money;
use Illuminate\Support\Collection;

class FinancialStatementService
{
    public function calculateBalance(Collection $entries) : Money
    {
        $totalIncome = $this->calculateTotalIncome($entries);
        $totalExpense = $this->calculateTotalExpense($entries);

        return $totalIncome->subtract($totalExpense);
    }

    public function calculateTotalIncome(Collection $entries) : Money
    {
        $incomes = $entries->filter(fn(Entry $entry) => !$entry->isExpense());

        return $incomes->reduce(
            fn(Money $carry, Entry $income) => $carry->add($income->getValue()),
            new Money(0)
        );
    }

    public function calculateTotalExpense(Collection $entries) : Money
    {
        $expenses = $entries->filter(fn(Entry $entry) => $entry->isExpense());

        return $expenses->reduce(
            fn(Money $carry, Entry $expense) => $carry->add($expense->getValue()),
            new Money(0)
        );
    }
}
