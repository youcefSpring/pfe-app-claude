<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefenseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'defense_id',
        'file_path',
        'generated_at',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    public function defense(): BelongsTo
    {
        return $this->belongsTo(Defense::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getDownloadUrl(): string
    {
        return route('reports.defense.download', $this->id);
    }

    public function exists(): bool
    {
        return file_exists(storage_path('app/' . $this->file_path));
    }
}