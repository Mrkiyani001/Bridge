<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionAttachmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'original_name' => $this->original_name,
            'stored_path'   => $this->stored_path,
            'mime_type'     => $this->mime_type,
            'size_bytes'    => (int) $this->size_bytes,
            'status'        => $this->status,
            'created_at'    => $this->created_at,
        ];
    }
}
