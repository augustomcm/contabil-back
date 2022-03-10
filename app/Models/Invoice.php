<?php

namespace App\Models;

use App\Helpers\Money;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $attributes = [
        'status' => InvoiceStatus::OPENED,
        'start_date' => null,
        'final_date' => null
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'start_date' => 'date',
        'final_date' => 'date'
    ];

    public function close()
    {
        if(now() < $this->final_date) {
            throw new \InvalidArgumentException('Current date less than final date.');
        }
        $this->status = InvoiceStatus::CLOSED;
        $this->save();
    }

    public function isClosed()
    {
        return $this->status === InvoiceStatus::CLOSED;
    }

    public function getTotal() : Money
    {
        $value = $this->entries->reduce(fn($carry, $item) => $carry + $item->getValue()->getAmountFloat());

        return Money::createByFloat($value ?? 0.00);
    }

    public function setPeriode(\DateTimeInterface $startDate, \DateTimeImmutable $finalDate)
    {
        if($finalDate < $startDate) {
            throw new \InvalidArgumentException("Final date must be greater than start date.");
        }

        $this->start_date = $startDate;
        $this->final_date = $finalDate;
    }

    public function setCreditCard(CreditCard $creditCard)
    {
        if($this->creditCard !== null) {
            throw new \InvalidArgumentException("Credit card has already been assigned.");
        }

        $this->creditCard()->associate($creditCard);
    }

    protected function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }

    public function addEntry(Entry $entry)
    {
        if($this->isClosed()) {
           throw new \InvalidArgumentException('Invoice is closed.');
        }

        $entry->setCreditCardType();
        $entry->save();

        $this->creditCard->debit($entry->getValue());
        $this->entries()->attach($entry);
    }

    public function removeEntry(Entry $entry)
    {
        if($this->isClosed()) {
            throw new \InvalidArgumentException('Invoice is closed.');
        }

        $this->creditCard->refunding($entry->getValue());
        $this->entries()->detach($entry);
    }

    public function getCreditCard() : CreditCard
    {
        return $this->creditCard;
    }

    /**
     * Use this method only within this class
     */
    public function entries()
    {
        return $this->belongsToMany(Entry::class);
    }
}
