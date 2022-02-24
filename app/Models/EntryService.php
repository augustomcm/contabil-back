<?php

namespace App\Models;

class EntryService
{
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
