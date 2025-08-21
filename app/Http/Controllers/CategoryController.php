<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    // GET /api/categories
    public function index(Request $request)
    {
        $q = Category::query();

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        return response()->json([
            'data' => $q->orderBy('name')->get(),
        ]);
    }

    // POST /api/categories
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required','string','max:191', Rule::unique('categories','name')],
            'description' => ['nullable','string','max:500'],
            'status'      => ['nullable','string','in:active,inactive,archived'],
        ]);

        $category = Category::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'] ?? 'active',
        ]);

        return response()->json(['data' => $category], 201);
    }
}
