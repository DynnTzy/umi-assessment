<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'sort_order',
    ];

    public function elements(): HasMany
    {
        return $this->hasMany(Element::class);
    }
}
