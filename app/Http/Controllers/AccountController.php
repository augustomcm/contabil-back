<?php

namespace App\Http\Controllers;

use App\Helpers\Money;
use App\Http\Resources\AccountResource;
use App\Models\AccountDefault;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index(Request $req)
    {
        $accounts = AccountDefault::where([
            'owner_id' => $req->user()->id
        ])->get();

        return AccountResource::collection($accounts);
    }

    public function store(Request $req)
    {
        $validated = $req->validate([
            'description' => 'required',
            'balance' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $owner = $req->user();

            $account = new AccountDefault([
                'description' => $validated['description'],
                'balance' => Money::createByFloat($validated['balance'])
            ]);

            $account->setOwner($owner);
            $account->save();

            DB::commit();

            return response()->json(new AccountResource($account), Response::HTTP_CREATED);
        }catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
