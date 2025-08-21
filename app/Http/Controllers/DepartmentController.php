<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    // GET /api/departments
    public function index(Request $request)
    {
        $q = Department::query();

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        return response()->json([
            'data' => $q->orderBy('name')->get(),
        ]);
    }

    // POST /api/departments
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required','string','max:191', Rule::unique('departments','name')],
            'description' => ['nullable','string','max:500'],
            'status'      => ['nullable','string','in:active,inactive,archived'],
        ]);

        $department = Department::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'] ?? 'active',
        ]);

        return response()->json(['data' => $department], 201);
    }
}
