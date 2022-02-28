<?php

namespace App\Http\Controllers;

use App\Http\Resources\EntryResource;
use App\Models\Entry;
use App\Models\EntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{
    public function index(Request $req)
    {
        $entries = Entry::where([
            'owner_id' => $req->user()->id
        ])->get();

        return EntryResource::collection($entries);
    }

    public function destroy(Request $req, Entry $entry, EntryService $entryService)
    {
        DB::beginTransaction();
        try {
            if($entry->owner()->isNot($req->user())) {
                abort(404);
            }

            $entryService->deleteEntry($entry);

            DB::commit();

            return response()->noContent();
        }catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
