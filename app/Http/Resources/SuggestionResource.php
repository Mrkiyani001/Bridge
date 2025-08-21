<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionResource extends JsonResource
{
    public function toArray($request): array
    {
        $isAnon = (bool)$this->is_anonymous;

        return [
            'id'            => $this->id,
            'is_anonymous'  => $isAnon,
            'name'          => $isAnon ? 'Anonymous' : $this->name,
            'designation'   => $isAnon ? null : $this->designation,
            'school_name'   => $isAnon ? null : $this->school_name,
            'email'         => $isAnon ? null : $this->email,
            'phone'         => $isAnon ? null : $this->phone,
            'theme'         => $this->theme,
            'subject'       => $this->subject,
            'details'       => $this->details,

            // Relations
            'category' => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ]),
            'department' => $this->whenLoaded('department', fn() => [
                'id'   => $this->department->id,
                'name' => $this->department->name,
            ]),

            // Attachments
            'attachments' => SuggestionAttachmentResource::collection(
                $this->whenLoaded('attachments')
            ),

            // Status & meta
            'status'       => $this->status,
            'deleted'      => (bool)$this->deleted,
            'submitted_at' => $this->submitted_at,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
