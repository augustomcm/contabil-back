<?php

namespace App\Models;

use App\Helpers\Interfaces\Money;

class EntryService
{
    public function createExpenseEntry(\DateTime $date, string $description, Money $value, User $owner, Category $category, CreditCard $creditCard = null) : Entry
    {
        $entry = new Entry([
            'description' => $description,
            'value' => $value,
            'type' => EntryType::EXPENSE,
            'date' => $date
        ]);

        $entry->owner()->associate($owner);
        $entry->setCategory($category);

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
