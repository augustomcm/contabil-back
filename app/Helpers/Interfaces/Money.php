<?php

namespace App\Helpers\Interfaces;

interface Money
{
    public function add(Money $money) : Money;
    public function subtract(Money $money) : Money;
    public function multiply(int|string $value) : Money;
    public function absolute() : Money;
    public function negative() : Money;
    public function equals(Money $money) :  bool;
    public function isNegative() : bool;
    public function allocateTo(int $n) : array;
    public function format(bool $absolute, string $locale) : string;
    public function getAmount() : string;
    public function getCurrencyCode() : string;
    public function greaterThan(Money $money) : bool;
}
