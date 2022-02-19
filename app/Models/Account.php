<?php

namespace App\Models;

use App\Helpers\Interfaces\Money;

interface Account
{
    public function deposit(Money $value);
    public function debit(Money $value);
    public function getBalance() : Money;
}
