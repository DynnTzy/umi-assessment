<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'asesor_id',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssessmentItem::class);
    }

    public function generateItems(): void
    {
        $elements = Element::orderBy('chapter_id')
            ->orderBy('sort_order')
            ->get();

        foreach ($elements as $element) {
            $this->items()->create([
                'element_id' => $element->id,
                'status' => 'pending',
            ]);
        }
    }
}
