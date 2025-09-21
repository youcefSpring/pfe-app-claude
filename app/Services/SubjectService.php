<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\User;
use App\Models\PfeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubjectService
{
    public function createSubject(array $data, User $supervisor): Subject
    {
        $this->validateSubjectData($data);

        return DB::transaction(function () use ($data, $supervisor) {
            $subject = Subject::create(array_merge($data, [
                'supervisor_id' => $supervisor->id,
                'status' => 'draft'
            ]));

            $this->notifySubjectCreated($subject);

            return $subject;
        });
    }

    public function submitSubject(Subject $subject): Subject
    {
        if ($subject->status !== 'draft' && $subject->status !== 'needs_correction') {
            throw ValidationException::withMessages([
                'status' => 'Subject can only be submitted from draft or needs_correction status'
            ]);
        }

        $subject->update(['status' => 'submitted']);

        $this->notifySubjectSubmitted($subject);

        return $subject;
    }

    public function validateSubject(Subject $subject, User $validator, string $action, ?string $notes = null): Subject
    {
        if ($subject->status !== 'submitted') {
            throw ValidationException::withMessages([
                'status' => 'Only submitted subjects can be validated'
            ]);
        }

        $allowedActions = ['approved', 'rejected', 'needs_correction'];
        if (!in_array($action, $allowedActions)) {
            throw ValidationException::withMessages([
                'action' => 'Invalid validation action'
            ]);
        }

        return DB::transaction(function () use ($subject, $validator, $action, $notes) {
            $subject->update([
                'status' => $action,
                'validated_by' => $validator->id,
                'validated_at' => now(),
                'validation_notes' => $notes
            ]);

            $this->notifySubjectValidated($subject, $action);

            return $subject;
        });
    }

    public function publishSubject(Subject $subject): Subject
    {
        if ($subject->status !== 'approved') {
            throw ValidationException::withMessages([
                'status' => 'Only approved subjects can be published'
            ]);
        }

        $subject->update(['status' => 'published']);

        $this->notifySubjectPublished($subject);

        return $subject;
    }

    public function getAvailableSubjects(): \Illuminate\Database\Eloquent\Collection
    {
        return Subject::where('status', 'published')
            ->whereDoesntHave('projects')
            ->with(['supervisor'])
            ->get();
    }

    private function validateSubjectData(array $data): void
    {
        if (!isset($data['keywords']) || count($data['keywords']) < 3) {
            throw ValidationException::withMessages([
                'keywords' => 'Subject must have at least 3 keywords'
            ]);
        }

        if (!isset($data['description']) || strlen($data['description']) < 100) {
            throw ValidationException::withMessages([
                'description' => 'Description must be at least 100 characters'
            ]);
        }
    }

    private function notifySubjectCreated(Subject $subject): void
    {
        PfeNotification::create([
            'user_id' => $subject->supervisor_id,
            'type' => 'subject_created',
            'title' => 'Subject Created',
            'message' => "Your subject '{$subject->title}' has been created successfully.",
            'data' => ['subject_id' => $subject->id]
        ]);
    }

    private function notifySubjectSubmitted(Subject $subject): void
    {
        // Notify department head
        $departmentHeads = User::role('chef_master')
            ->where('department', $subject->supervisor->department)
            ->get();

        foreach ($departmentHeads as $head) {
            PfeNotification::create([
                'user_id' => $head->id,
                'type' => 'subject_submitted',
                'title' => 'New Subject for Validation',
                'message' => "Subject '{$subject->title}' by {$subject->supervisor->first_name} {$subject->supervisor->last_name} needs validation.",
                'data' => ['subject_id' => $subject->id]
            ]);
        }
    }

    private function notifySubjectValidated(Subject $subject, string $action): void
    {
        $messages = [
            'approved' => 'Your subject has been approved',
            'rejected' => 'Your subject has been rejected',
            'needs_correction' => 'Your subject needs corrections'
        ];

        PfeNotification::create([
            'user_id' => $subject->supervisor_id,
            'type' => "subject_{$action}",
            'title' => 'Subject Validation Update',
            'message' => $messages[$action] . ": {$subject->title}",
            'data' => ['subject_id' => $subject->id]
        ]);
    }

    private function notifySubjectPublished(Subject $subject): void
    {
        // Notify all students
        $students = User::role('student')->get();

        foreach ($students as $student) {
            PfeNotification::create([
                'user_id' => $student->id,
                'type' => 'subject_published',
                'title' => 'New Subject Available',
                'message' => "New PFE subject available: {$subject->title}",
                'data' => ['subject_id' => $subject->id]
            ]);
        }
    }
}