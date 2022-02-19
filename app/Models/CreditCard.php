<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    protected $fillable = [
        'description',
        'closing_day',
        'expiration_day'
    ];

    protected static function boot()
    {
        parent::boot();
        self::created(function(CreditCard $creditCard) {
            $startDate = now()->setDay($creditCard->closing_day)->startOfDay()->toImmutable();

            $invoice = new Invoice();

            $invoice->setPeriode($startDate, $startDate->addMonth());
            $invoice->setCreditCard($creditCard);
            $invoice->save();
        });
    }
}
