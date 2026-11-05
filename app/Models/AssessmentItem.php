<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'element_id',
        'status',
        'notes',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssessmentDocument::class);
    }
}
