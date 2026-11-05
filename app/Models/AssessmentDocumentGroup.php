<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssessmentDocumentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_item_id',
        'created_by',
        'instrument_type',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
        ];
    }

    public function assessmentItem(): BelongsTo
    {
        return $this->belongsTo(AssessmentItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(AssessmentDocument::class, 'document_group_id')
            ->orderByDesc('version_number');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(AssessmentDocument::class, 'document_group_id')
            ->ofMany('version_number', 'max');
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }
}
