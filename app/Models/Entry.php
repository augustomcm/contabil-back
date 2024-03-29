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
        'description',
        'value',
        'type',
        'date'
    ];

    protected $casts = [
        'value' => MoneyCast::class,
        'status' => EntryStatus::class,
        'payment_type' => EntryPaymentType::class,
        'paid_at' => 'date',
        'type' => EntryType::class,
        'date' => 'date'
    ];

    protected $attributes = [
        'type' => EntryType::EXPENSE,
        'status' => EntryStatus::PENDING,
        'payment_type' => EntryPaymentType::DEFAULT
    ];

    public function setCategory(Category $category)
    {
        if(!$category->owner->is($this->owner) || $category->type !== $this->type) {
            throw new \InvalidArgumentException("Invalid category.");
        }

        $this->category()->associate($category);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getValue() : Money
    {
        return $this->value;
    }

    public function pay(Account $account, \DateTimeInterface $dateTime)
    {
        if($this->isPaid()) {
            return;
        }

        if($this->isExpense()) {
            $account->debit($this->value);
        }else{
            $account->deposit($this->value);
        }

        $this->paid_at = $dateTime;
        $this->status = EntryStatus::PAID;

        $this->account()->associate($account);

        $this->save();
    }

    public function cancelPayment()
    {
        if(!$this->isPaid()) {
            return;
        }

        if($this->isExpense()) {
            $this->getAccount()->deposit($this->value);
        }else{
            $this->getAccount()->debit($this->value);
        }

        $this->getAccount()->save(); // consequences of active record :/

        $this->account()->dissociate();
        $this->paid_at = null;
        $this->status = EntryStatus::PENDING;

        $this->save();
    }

    public function isExpense()
    {
        return $this->type === EntryType::EXPENSE;
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

    public function setCreditCardType()
    {
        $this->payment_type = EntryPaymentType::CREDIT_CARD;
    }

    public function isCreditCardType()
    {
        return $this->payment_type === EntryPaymentType::CREDIT_CARD;
    }

    public function setOwner(User $user)
    {
        $this->owner()->associate($user);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
