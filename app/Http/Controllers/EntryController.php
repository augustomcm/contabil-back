<?php

namespace App\Http\Controllers;

use App\Http\Resources\EntryResource;
use App\Models\Entry;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    public function index(Request $req)
    {
        $entries = Entry::where([
            'owner_id' => $req->user()->id
        ])->get();

        return EntryResource::collection($entries);
    }
}
