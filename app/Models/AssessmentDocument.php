<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssessmentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_item_id',
        'upload_action',
        'upload_note',
        'uploaded_by',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'instrument_type',
    ];

    public function assessmentItem(): BelongsTo
    {
        return $this->belongsTo(AssessmentItem::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AssessmentDocumentReview::class);
    }

    public function latestReview(): HasOne
    {
        return $this->hasOne(AssessmentDocumentReview::class)
            ->latestOfMany();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AssessmentDocumentActivity::class)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }
}
