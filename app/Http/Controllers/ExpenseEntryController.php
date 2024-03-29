<?php

namespace App\Http\Controllers;

use App\Helpers\Money;
use App\Http\Resources\EntryResource;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\EntryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ExpenseEntryController extends Controller
{
    public function store(Request $req, EntryService $entryService)
    {
        $validated = $req->validate([
            'description' => 'required',
            'value' => 'required',
            'credit_card_id' => 'nullable',
            'category_id' => 'required',
            'date' => 'required|date'
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
                new \DateTime($validated['date']),
                $validated['description'],
                Money::createByFloat($validated['value']),
                $owner,
                Category::findOrFail($validated['category_id']),
                $creditCard
            );

            DB::commit();

            return response()->json(new EntryResource($entry), Response::HTTP_CREATED);
        }catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
