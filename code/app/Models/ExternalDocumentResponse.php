<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalDocumentResponse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_document_id',
        'team_id',
        'file_path',
        'file_original_name',
        'file_size',
        'file_type',
        'uploaded_by',
        'admin_feedback',
        'feedback_by',
        'feedback_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'feedback_at' => 'datetime',
        ];
    }

    /**
     * Get the external document this response belongs to.
     */
    public function externalDocument(): BelongsTo
    {
        return $this->belongsTo(ExternalDocument::class);
    }

    /**
     * Get the team that submitted this response.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user who uploaded the response.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the admin who provided feedback.
     */
    public function feedbackProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'feedback_by');
    }

    /**
     * Check if this response has feedback.
     */
    public function hasFeedback(): bool
    {
        return !empty($this->admin_feedback);
    }

    /**
     * Get the file size in human-readable format.
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
