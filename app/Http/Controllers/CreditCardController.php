<?php

namespace App\Http\Controllers;

use App\Http\Resources\CreditCardResource;
use App\Models\CreditCard;
use Illuminate\Http\Request;

class CreditCardController extends Controller
{
    public function index(Request $req)
    {
        $creditCards = CreditCard::with('invoices')->where([
            'owner_id' => $req->user()->id
        ])->get();

        return CreditCardResource::collection($creditCards);
    }
}
