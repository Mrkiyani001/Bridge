<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suggestion extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'is_anonymous',
        'name',
        'designation',
        'school_name',
        'email',
        'phone',
        'theme',
        'category_id',
        'department_id',
        'subject',
        'details',
        'deleted',
        'submitted_at',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'deleted'      => 'boolean',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // Only active attachments visible by default
    public function attachments(): HasMany
    {
        return $this->hasMany(SuggestionAttachment::class)->where('status', 'active');
    }

    // All attachments including inactive
    public function allAttachments(): HasMany
    {
        return $this->hasMany(SuggestionAttachment::class);
    }
}
