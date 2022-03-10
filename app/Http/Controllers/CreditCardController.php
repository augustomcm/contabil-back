<?php

namespace App\Http\Controllers;

use App\Http\Resources\CreditCardResource;
use App\Models\AccountDefault;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditCardController extends Controller
{
    public function index(Request $req)
    {
        $creditCards = CreditCard::with('invoices')->where([
            'owner_id' => $req->user()->id
        ])->get();

        return CreditCardResource::collection($creditCards);
    }

    public function closeInvoice(Request $req, CreditCard $creditCard)
    {
        DB::beginTransaction();
        try {
            if($creditCard->owner()->isNot($req->user())) {
                abort(404);
            }

            $creditCard->closeCurrentInvoice();

            DB::commit();

            return response()->noContent();
        }catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function payInvoice(Request $req, CreditCard $creditCard)
    {
        DB::beginTransaction();
        try {
            $validated = $req->validate([
                'account' => 'required',
                'date' => 'date'
            ]);

            if($creditCard->owner()->isNot($req->user())) {
                abort(404);
            }

            $account = AccountDefault::findOrFail($validated['account']);

            $creditCard->payCurrentInvoice($account);

            DB::commit();

            return response()->noContent();
        }catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
