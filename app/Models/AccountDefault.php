<?php

namespace App\Models;

use App\Helpers\Interfaces\Money;
use App\Helpers\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountDefault extends Model implements Account
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'balance'
    ];

    protected $casts = [
        'balance' => MoneyCast::class
    ];

    public function deposit(Money $value)
    {
        $absoluteValue = $value->absolute();
        $this->balance = $this->getBalance()->add($absoluteValue);
    }

    public function debit(Money $value)
    {
        $absoluteValue = $value->absolute();
        if($absoluteValue->greaterThan($this->balance))
            throw new \InvalidArgumentException("Insufficient funds.");

        $this->balance = $this->getBalance()->subtract($absoluteValue);
    }

    public function getBalance() : Money
    {
        return $this->balance;
    }
}