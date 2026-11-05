<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Element extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'code',
        'sort_order',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function assessmentItems(): HasMany
    {
        return $this->hasMany(AssessmentItem::class);
    }
}
