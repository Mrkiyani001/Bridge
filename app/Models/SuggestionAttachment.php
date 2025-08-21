<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuggestionAttachment extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'suggestion_id',
        'original_name',
        'stored_path',
        'mime_type',
        'size_bytes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
    ];

    public function suggestion(): BelongsTo
    {
        return $this->belongsTo(Suggestion::class);
    }
}
