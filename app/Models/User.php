<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'student_id',
        'department',
        'avatar_path',
        'is_active',
        'name',
        'role',
        'status',
        'bio',
        'profile_picture',
        'contact_info',
        'cv_file_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'contact_info' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the courses for the user.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the projects for the user.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the publications for the user.
     */
    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    /**
     * Get the blog posts for the user.
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Get the credentials for the user.
     */
    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class);
    }

    /**
     * Get the subjects supervised by the user.
     */
    public function supervisedSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'supervisor_id');
    }

    /**
     * Get the subjects validated by the user.
     */
    public function validatedSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'validated_by');
    }

    /**
     * Get the teams led by the user.
     */
    public function ledTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'leader_id');
    }

    /**
     * Get the team memberships for the user.
     */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get the PFE projects supervised by the user.
     */
    public function supervisedPfeProjects(): HasMany
    {
        return $this->hasMany(PfeProject::class, 'supervisor_id');
    }

    /**
     * Get the deliverables submitted by the user.
     */
    public function submittedDeliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class, 'submitted_by');
    }

    /**
     * Get the deliverables reviewed by the user.
     */
    public function reviewedDeliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class, 'reviewed_by');
    }

    /**
     * Get the defenses where the user is jury president.
     */
    public function presidedDefenses(): HasMany
    {
        return $this->hasMany(Defense::class, 'jury_president_id');
    }

    /**
     * Get the defenses where the user is examiner.
     */
    public function examinedDefenses(): HasMany
    {
        return $this->hasMany(Defense::class, 'jury_examiner_id');
    }

    /**
     * Get the defenses where the user is supervisor.
     */
    public function supervisedDefenses(): HasMany
    {
        return $this->hasMany(Defense::class, 'jury_supervisor_id');
    }

    /**
     * Get the notifications for the user.
     */
    public function pfeNotifications(): HasMany
    {
        return $this->hasMany(PfeNotification::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is editor.
     */
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}