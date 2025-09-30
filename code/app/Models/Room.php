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
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
        ];
    }

    public function defenses(): HasMany
    {
        return $this->hasMany(Defense::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function isAvailableAt($date, $time, $duration = 60): bool
    {
        return !$this->defenses()
            ->where('defense_date', $date)
            ->where(function ($query) use ($time, $duration) {
                $endTime = date('H:i:s', strtotime($time) + ($duration * 60));
                $query->whereBetween('defense_time', [$time, $endTime])
                    ->orWhere(function ($q) use ($time, $endTime) {
                        $q->where('defense_time', '<=', $time)
                          ->whereRaw('ADDTIME(defense_time, SEC_TO_TIME(duration * 60)) > ?', [$time]);
                    });
            })->exists();
    }
}