<?php

namespace App\Models;

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
        $this->creditCard->debit($entry->getValue());
        $this->entries()->attach($entry);
    }

    public function removeEntry(Entry $entry)
    {
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
