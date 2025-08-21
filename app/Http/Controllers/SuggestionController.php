<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use App\Models\SuggestionAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SuggestionResource;
use Illuminate\Http\Response;

class SuggestionController extends Controller
{
    // GET /api/suggestions
    public function index(Request $request)
    {
        $q = Suggestion::query()->with(['category:id,name','department:id,name','attachments']);

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        if ($request->filled('category_id')) {
            $q->where('category_id', (int)$request->category_id);
        }
        if ($request->filled('department_id')) {
            $q->where('department_id', (int)$request->department_id);
        }
        if ($request->filled('search')) {
            $s = $request->string('search');
            $q->where(function ($qq) use ($s) {
                $qq->where('theme', 'like', "%{$s}%")
                   ->orWhere('subject', 'like', "%{$s}%")
                   ->orWhere('details', 'like', "%{$s}%");
            });
        }

        // Pagination with frontend control
        $min = max(1, (int)$request->query('min_per_page', 5));
        $max = (int)$request->query('max_per_page', 5000);
        $min = max(1, min($min, 200));
        $max = max($min, min($max, 5000));
        $perPage = max($min, min((int)$request->query('per_page', $min), $max));

        $paginator = $q->orderByDesc('id')->paginate($perPage);

        return SuggestionResource::collection($paginator)
            ->additional([
                'meta' => [
                    'per_page'     => $perPage,
                    'min_per_page' => $min,
                    'max_per_page' => $max,
                    'total'        => $paginator->total(),
                ],
            ]);
    }

    // GET /api/suggestions/{id}
    public function show($id)
    {
        $suggestion = Suggestion::with(['category:id,name','department:id,name','attachments'])
            ->findOrFail($id);

        return new SuggestionResource($suggestion);
    }

    // POST /api/suggestions
    public function store(Request $request)
    {
        $validated = $request->validate([
            'is_anonymous'  => ['nullable','boolean'],
            'name'          => ['required','string','max:191'],
            'designation'   => ['required','string','max:191'],
            'school_name'   => ['required','string','max:255'],
            'email'         => ['required','email','max:191'],
            'phone'         => ['nullable','string','max:50'],
            'theme'         => ['required','string','max:255'],
            'category_id'   => ['required','integer','exists:categories,id'],
            'department_id' => ['required','integer','exists:departments,id'],
            'subject'       => ['nullable','string'],
            'details'       => ['required','string'],
            'status'        => ['nullable','string','in:draft,submitted,under_review,approved,rejected,archived'],
            'attachments'   => ['sometimes'],
            'attachments.*' => ['file','max:10240',
                'mimetypes:image/png,image/jpeg,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,video/mp4,video/x-matroska'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $suggestion = Suggestion::create([
                'is_anonymous'  => (bool)($validated['is_anonymous'] ?? false),
                'name'          => $validated['name'],
                'designation'   => $validated['designation'],
                'school_name'   => $validated['school_name'],
                'email'         => $validated['email'],
                'phone'         => $validated['phone'] ?? null,
                'theme'         => $validated['theme'],
                'category_id'   => $validated['category_id'],
                'department_id' => $validated['department_id'],
                'subject'       => $validated['subject'] ?? null,
                'details'       => $validated['details'],
                'deleted'       => false,
                'submitted_at'  => now(),
                'status'        => $validated['status'] ?? 'submitted',
            ]);

            // Handle single or multiple attachments
            $files = [];
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                if (!is_array($files)) {
                    $files = [$files];
                }
            }

            foreach ($files as $file) {
                $path = $file->store('public/suggestion_attachments');
                $publicPath = Storage::url($path);

                SuggestionAttachment::create([
                    'suggestion_id' => $suggestion->id,
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path'   => $publicPath,
                    'mime_type'     => $file->getClientMimeType(),
                    'size_bytes'    => $file->getSize(),
                    'status'        => 'active',
                ]);
            }

            $suggestion->load(['category:id,name','department:id,name','attachments']);

            return (new SuggestionResource($suggestion))
                ->response()
                ->setStatusCode(201);
        });
    }

    // DELETE /api/v1/attachments/{id} -> mark as removed (soft delete)
    public function deactivateAttachment($id)
    {
        $attachment = SuggestionAttachment::findOrFail($id);

        if ($attachment->status !== 'removed') {
            $attachment->status = 'removed';
            $attachment->save();
        }

        return response()->json([
            'message' => 'Attachment removed (status=removed).',
            'data'    => $attachment
        ], Response::HTTP_OK);
    }

    // PATCH /api/v1/attachments/{id}/status -> set active/inactive/removed
    public function setAttachmentStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required','string','in:active,inactive,removed'],
        ]);

        $attachment = SuggestionAttachment::findOrFail($id);
        $attachment->status = $validated['status'];
        $attachment->save();

        return response()->json([
            'message' => "Attachment status updated to {$attachment->status}.",
            'data'    => $attachment
        ], Response::HTTP_OK);
    }
}