<?php

namespace App\Http\Controllers;

use App\Helpers\Money;
use App\Http\Resources\EntryResource;
use App\Models\Category;
use App\Models\EntryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function store(Request $req, EntryService $entryService)
    {
        $validated = $req->validate([
            'description' => 'required',
            'value' => 'required',
            'category_id' => 'required',
            'date' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $owner = $req->user();

            $entry = $entryService->createIncomeEntry(
                new \DateTime($validated['date']),
                $validated['description'],
                Money::createByFloat($validated['value']),
                $owner,
                Category::findOrFail($validated['category_id'])
            );

            DB::commit();

            return response()->json(new EntryResource($entry), Response::HTTP_CREATED);
        }catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
