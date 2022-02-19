<?php

namespace App\Models;

use App\Helpers\Interfaces\Money;
use App\Helpers\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'closing_day',
        'expiration_day',
        'limit'
    ];

    protected $casts = [
        'limit' => MoneyCast::class
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

    public function debit(Money $money)
    {
        $money = $money->absolute();
        if($money->greaterThan($this->limit)) {
            throw new \InvalidArgumentException("Insufficient limit.");
        }

        $this->limit = $this->limit->subtract($money);

        $this->save();
    }

    public function refunding(Money $money)
    {
        $money = $money->absolute();
        $this->limit = $this->limit->add($money);

        $this->save();
    }

    public function getCurrentInvoice() : Invoice
    {
        return $this->invoices()->orderBy('created_at', 'desc')->first();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
