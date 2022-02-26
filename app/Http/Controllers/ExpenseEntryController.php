<?php

namespace App\Http\Controllers;

use App\Helpers\Money;
use App\Models\CreditCard;
use App\Models\EntryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseEntryController extends Controller
{
    public function store(Request $req, EntryService $entryService)
    {
        $validated = $req->validate([
            'value' => 'required',
            'credit_card_id' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $owner = $req->user();
            $creditCard = null;

            if(!empty($validated['credit_card_id'])) {
                $creditCard = CreditCard::where('id', $validated['credit_card_id'])
                    ->where('owner_id', $owner->id)
                    ->firstOrFail();
            }

            $entry = $entryService->createExpenseEntry(
                $owner,
                Money::createByFloat($validated['value']),
                $creditCard
            );

            DB::commit();

            return response()->json($entry, Response::HTTP_CREATED);
        }catch (\Exception $ex) {
            DB::rollBack();
            Log::critical($ex->getMessage());
            return response()->json('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
