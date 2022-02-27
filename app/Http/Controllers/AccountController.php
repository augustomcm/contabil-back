<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountResource;
use App\Models\AccountDefault;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $req)
    {
        $accounts = AccountDefault::where([
            'owner_id' => $req->user()->id
        ])->get();

        return AccountResource::collection($accounts);
    }
}
