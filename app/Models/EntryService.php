<?php

namespace App\Models;

use App\Helpers\Interfaces\Money;

class EntryService
{
    public function createExpenseEntry(User $owner, Money $value, CreditCard $creditCard = null) : Entry
    {
        $entry = new Entry([
            'value' => $value,
            'type' => EntryType::EXPENSE
        ]);

        $entry->owner()->associate($owner);

        $entry->save();

        if($creditCard !== null) {
            $invoice = $creditCard->getCurrentInvoice();
            $invoice->addEntry($entry);
        }

        $entry->getValue()->format();
        return $entry;
    }

    public function deleteEntry(Entry $entry)
    {
        $entry->cancelPayment();
        if($entry->isCreditCardType()) {
            $invoice = Invoice::whereHas('entries', function($q) use ($entry){
                $q->where('entries.id', $entry->id);
            })->first();

            $invoice->removeEntry($entry);
        }

        $entry->delete();
    }
}
