<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalDocumentDeadline extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'academic_year',
        'upload_start',
        'upload_deadline',
        'response_start',
        'response_deadline',
        'status',
        'description',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'upload_start' => 'datetime',
            'upload_deadline' => 'datetime',
            'response_start' => 'datetime',
            'response_deadline' => 'datetime',
        ];
    }

    /**
     * Get the user who created this deadline.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if admin can upload documents now.
     */
    public function canUploadDocuments(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = Carbon::now();
        return (!$this->upload_start || $now->greaterThanOrEqualTo($this->upload_start))
            && (!$this->upload_deadline || $now->lessThanOrEqualTo($this->upload_deadline));
    }

    /**
     * Check if teams can submit responses now.
     */
    public function canSubmitResponses(): bool
    {
        if (!in_array($this->status, ['active', 'upload_closed'])) {
            return false;
        }

        $now = Carbon::now();
        return (!$this->response_start || $now->greaterThanOrEqualTo($this->response_start))
            && (!$this->response_deadline || $now->lessThanOrEqualTo($this->response_deadline));
    }

    /**
     * Get the active deadline for the current academic year.
     */
    public static function getActive(): ?self
    {
        return self::where('status', 'active')->first();
    }
}
