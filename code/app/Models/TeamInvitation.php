<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TeamInvitation extends Model
{
    protected $fillable = [
        'team_id',
        'invited_email',
        'invited_by',
        'status',
        'token',
        'expires_at',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function invitedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_email', 'email');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    public function canRespond(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }

    public function accept(): bool
    {
        if (!$this->canRespond()) {
            return false;
        }

        $user = User::where('email', $this->invited_email)->first();
        if (!$user || $user->teamMember) {
            return false;
        }

        if ($this->team->members()->count() >= 4) {
            return false;
        }

        \DB::beginTransaction();
        try {
            TeamMember::create([
                'team_id' => $this->team_id,
                'student_id' => $user->id,
                'role' => 'member',
                'joined_at' => now()
            ]);

            $this->update([
                'status' => 'accepted',
                'responded_at' => now()
            ]);

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            return false;
        }
    }

    public function decline(): bool
    {
        if (!$this->canRespond()) {
            return false;
        }

        $this->update([
            'status' => 'declined',
            'responded_at' => now()
        ]);

        return true;
    }

    public static function createInvitation(Team $team, string $email, User $invitedBy): ?self
    {
        if ($team->members()->count() >= 4) {
            return null;
        }

        $user = User::where('email', $email)->where('role', 'student')->first();
        if (!$user) {
            return null;
        }

        if ($user->teamMember) {
            return null;
        }

        $existingInvitation = self::where('team_id', $team->id)
            ->where('invited_email', $email)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation && !$existingInvitation->isExpired()) {
            return null;
        }

        return self::create([
            'team_id' => $team->id,
            'invited_email' => $email,
            'invited_by' => $invitedBy->id,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (empty($invitation->token)) {
                $invitation->token = Str::random(64);
            }
            if (empty($invitation->expires_at)) {
                $invitation->expires_at = now()->addDays(7);
            }
        });
    }
}
