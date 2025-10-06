<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'matricule',
        'phone',
        'department',
        'position',
        'speciality',
        'grade',
        'role',
        'locale',
        'speciality_id',
        'numero_inscription',
        'annee_bac',
        'first_name',
        'last_name',
        'section',
        'groupe',
        'date_naissance',
        'lieu_naissance',
        'status',
        'academic_year',
        'profile_completed',
        'email_verified_at',
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
            'date_naissance' => 'date',
            'annee_bac' => 'integer',
        ];
    }

    // Speciality Relationship
    /**
     * Get the speciality that the user belongs to.
     */
    // public function speciality(): BelongsTo
    // {
    //     return $this->belongsTo(Speciality::class);
    // }

    // Relationships for Teachers
    /**
     * Get the subjects created by this teacher.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    /**
     * Get the marks for this student.
     */
    public function marks(): HasMany
    {
        return $this->hasMany(StudentMark::class, 'user_id');
    }

    /**
     * Get the average mark for this student.
     */
    public function getAverageMarkAttribute(): float
    {
        return $this->marks()->avg('mark') ?? 0;
    }

    /**
     * Get the average percentage for this student.
     */
    public function getAveragePercentageAttribute(): float
    {
        $marks = $this->marks()->get();
        if ($marks->isEmpty()) {
            return 0;
        }

        $totalPercentage = $marks->sum(function($mark) {
            return $mark->percentage;
        });

        return round($totalPercentage / $marks->count(), 2);
    }

    /**
     * Get the projects supervised by this teacher.
     */
    public function supervisedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    /**
     * Get the external projects assigned to this teacher.
     */
    public function assignedExternalProjects(): HasMany
    {
        return $this->hasMany(ExternalProject::class, 'assigned_supervisor_id');
    }

    /**
     * Get the jury participations for this teacher.
     */
    public function juryParticipations(): HasMany
    {
        return $this->hasMany(DefenseJury::class, 'teacher_id');
    }

    /**
     * Alias for juryParticipations (used in views).
     */
    public function juryAssignments(): HasMany
    {
        return $this->juryParticipations();
    }

    // Relationships for Students
    /**
     * Get the team memberships for this student.
     */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'student_id');
    }

    /**
     * Get the current team membership for this student (single relationship).
     */
    public function teamMember()
    {
        return $this->hasOne(TeamMember::class, 'student_id');
    }

    /**
     * Get the current active team for this student.
     */
    public function activeTeam()
    {
        return $this->teamMemberships()
            ->whereHas('team', function ($query) {
                $query->where('status', 'active');
            })
            ->with('team')
            ->first()?->team;
    }

    /**
     * Get the subject preferences for this student.
     */
    public function subjectPreferences(): HasMany
    {
        return $this->hasMany(SubjectPreference::class, 'student_id');
    }

    /**
     * Get the grades for this student.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class, 'student_id');
    }

    /**
     * Get the subject allocation for this student.
     */
    public function subjectAllocation()
    {
        return $this->hasOne(SubjectAllocation::class, 'student_id');
    }

    // Relationships for Department Heads
    /**
     * Get the subjects validated by this department head.
     */
    public function validatedSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'validated_by');
    }

    /**
     * Get the conflicts resolved by this department head.
     */
    public function resolvedConflicts(): HasMany
    {
        return $this->hasMany(SubjectConflict::class, 'resolved_by');
    }

    // Relationships for Admins
    /**
     * Get the defense reports generated by this admin.
     */
    public function generatedReports(): HasMany
    {
        return $this->hasMany(DefenseReport::class, 'generated_by');
    }

    // Role Helper Methods
    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is a department head.
     */
    public function isDepartmentHead(): bool
    {
        return $this->role === 'department_head';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an external supervisor.
     */
    public function isExternalSupervisor(): bool
    {
        return $this->role === 'external_supervisor';
    }

    // Business Logic Methods
    /**
     * Get the current workload for teachers (number of active projects).
     */
    public function getCurrentWorkload(): int
    {
        if (!$this->isTeacher()) {
            return 0;
        }

        return $this->supervisedProjects()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count();
    }

    /**
     * Check if teacher can supervise more projects.
     */
    public function canSuperviseMoreProjects(): bool
    {
        if (!$this->isTeacher()) {
            return false;
        }

        return $this->getCurrentWorkload() < 5; // Max 5 projects per teacher
    }

    /**
     * Get specialty match score for external project assignment.
     */
    public function getSpecialtyMatchScore(string $domain): float
    {
        if (!$this->speciality) {
            return 0.0;
        }

        // Simple text similarity - in real implementation, use more sophisticated matching
        similar_text(strtolower($this->speciality), strtolower($domain), $percent);
        return $percent / 100;
    }
}
