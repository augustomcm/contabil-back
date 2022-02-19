<?php

namespace App\Models;

use App\Helpers\Interfaces\Money;
use App\Helpers\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'type'
    ];

    protected $casts = [
        'value' => MoneyCast::class,
        'status' => EntryStatus::class,
        'type' => EntryType::class,
        'paid_at' => 'date'
    ];

    protected $attributes = [
        'status' => EntryStatus::PENDING
    ];

    public function pay(Account $account, \DateTimeInterface $dateTime)
    {
        if($this->isPaid()) {
            return;
        }

        $account->debit($this->value);
        $this->paid_at = $dateTime;
        $this->status = EntryStatus::PAID;

        $this->account()->associate($account);
    }

    public function cancelPayment()
    {
        if(!$this->isPaid()) {
            return;
        }

        $this->getAccount()->deposit($this->value);
        $this->account()->dissociate($this->account);
        $this->paid_at = null;
        $this->status = EntryStatus::PENDING;
    }

    public function getAccount() : ?Account
    {
        return $this->account;
    }

    protected function account()
    {
        return $this->morphTo();
    }

    public function isPaid()
    {
        return $this->status === EntryStatus::PAID;
    }
}
