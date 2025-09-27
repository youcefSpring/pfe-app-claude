<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'equipment',
        'is_available',
        'location',
    ];

    protected $casts = [
        'equipment' => 'array',
        'is_available' => 'boolean',
    ];

    public function defenses(): HasMany
    {
        return $this->hasMany(Defense::class);
    }
}