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

    public function getLimit() : Money
    {
        return $this->limit;
    }

    protected static function boot()
    {
        parent::boot();
        self::created(function(CreditCard $creditCard) {
            $creditCard->createNewInvoice();
        });
    }

    private function createNewInvoice()
    {
        $currentDate = now();
        if($this->closing_day > $currentDate->day) {
            $startDate = $currentDate
                ->subMonth()
                ->setDay($this->closing_day)
                ->startOfDay()
                ->toImmutable();
        }else{
            $startDate = $currentDate
                ->setDay($this->closing_day)
                ->startOfDay()
                ->toImmutable();
        }

        $invoice = new Invoice();

        $invoice->setPeriode($startDate, $startDate->addMonth());
        $invoice->setCreditCard($this);
        $invoice->save();
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

    public function closeCurrentInvoice()
    {
        $this->getCurrentInvoice()->close();
    }

    public function payCurrentInvoice(Account $account)
    {
        if($account->owner()->isNot($this->owner)) {
            throw new \InvalidArgumentException("Invalid account.");
        }

        $this->getCurrentInvoice()->pay($account);
        $this->createNewInvoice();
    }

    public function getCurrentInvoice() : Invoice
    {
        return $this->invoices()->orderBy('id', 'desc')->first();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function setOwner(User $owner)
    {
        $this->owner()->associate($owner);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
