<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentDocumentReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_document_id',
        'reviewer_id',
        'status',
        'notes',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(
            AssessmentDocument::class,
            'assessment_document_id'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewer_id'
        );
    }
}
