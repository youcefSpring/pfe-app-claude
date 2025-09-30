<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speciality extends Model
{
    protected $fillable = [
        'name',
        'code',
        'level',
        'academic_year',
        'semester',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the students for the speciality.
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    /**
     * Get all users for the speciality.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope a query to only include active specialities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full name of the speciality.
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ' (' . $this->level . ' - ' . $this->academic_year . ')';
    }

    /**
     * Get the student count for this speciality.
     */
    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }
}
