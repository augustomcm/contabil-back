<?php

namespace App\Http\Controllers;

use App\Http\Resources\EntryResource;
use App\Models\Entry;
use App\Models\EntryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            //TODO como lidar com exceções de validação no laravel
            if($entry->owner()->isNot($req->user())) {
                throw new \RuntimeException("Not found entry.");
            }

            $entryService->deleteEntry($entry);

            DB::commit();

            return response()->noContent();
        }catch (\Exception $ex) {
            DB::rollBack();
            Log::critical($ex->getMessage());

            return response()->json('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
