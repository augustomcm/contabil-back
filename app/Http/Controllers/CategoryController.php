<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\EntryType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class CategoryController extends Controller
{
    public function index(Request $req)
    {
        $validated = $req->validate([
            'type' => [new Enum(EntryType::class), 'nullable']
        ]);

        $query = Category::where([
            'owner_id' => $req->user()->id
        ]);

        if(!empty($validated['type'])) $query->where('type', $validated['type']);

        $categories = $query->get();

        return CategoryResource::collection($categories);
    }
}
