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
}
