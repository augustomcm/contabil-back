<?php

namespace App\Http\Controllers;

use App\Http\Resources\EntryResource;
use App\Models\Account;
use App\Models\AccountDefault;
use App\Models\Entry;
use App\Models\EntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{
    public function index(Request $req)
    {
        $validated = $req->validate([
            'month' => 'nullable|min:1|max:12'
        ]);

        $month = !empty($validated['month']) ? $validated['month'] : date('m');

        $initalDate = \Carbon\CarbonImmutable::createMidnightDate(date('Y'), $month, 1);
        $finalDate = $initalDate->lastOfMonth();

        $entries = Entry::where([
            'owner_id' => $req->user()->id
        ])->whereBetween('date', [$initalDate->format('Y-m-d'), $finalDate->format('Y-m-d')])
         ->orderBy('date', 'desc')
         ->orderBy('id', 'desc')
         ->get();

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

    public function pay(Request $req, Entry $entry)
    {
        DB::beginTransaction();
        try {
            $validated = $req->validate([
                'account' => 'required',
                'date' => 'date'
            ]);

            $account = AccountDefault::findOrFail($validated['account']);

            if($entry->owner()->isNot($req->user())) {
                abort(404);
            }

            $entry->pay($account, \DateTimeImmutable::createFromFormat('Y-m-d', $validated['date']));

            DB::commit();

            return response()->noContent();
        }catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function cancelPayment(Request $req, Entry $entry)
    {
        DB::beginTransaction();
        try {
            if($entry->owner()->isNot($req->user())) {
                abort(404);
            }

            $entry->cancelPayment();

            DB::commit();

            return response()->noContent();
        }catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
