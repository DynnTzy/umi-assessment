<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentDocumentActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_document_id',
        'user_id',
        'type',
        'file_name',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
