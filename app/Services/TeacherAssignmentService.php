<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Collection;

class TeacherAssignmentService
{
    /**
     * Assign teachers to external subjects based on keyword matching.
     */
    public function assignTeachersToExternalSubjects(): array
    {
        $results = [
            'total_subjects' => 0,
            'assigned_subjects' => 0,
            'assignments' => []
        ];

        // Get all external subjects without assigned teachers
        $externalSubjects = Subject::where('is_external', true)
            ->where('status', 'validated')
            ->whereNull('teacher_id')
            ->get();

        $results['total_subjects'] = $externalSubjects->count();

        foreach ($externalSubjects as $subject) {
            $assignment = $this->findBestTeacherForSubject($subject);

            if ($assignment['teacher']) {
                $this->assignTeacherToSubject($assignment['teacher'], $subject);
                $results['assigned_subjects']++;
                $results['assignments'][] = [
                    'subject' => $subject,
                    'teacher' => $assignment['teacher'],
                    'match_score' => $assignment['score'],
                    'matching_keywords' => $assignment['matching_keywords']
                ];
            }
        }

        return $results;
    }

    /**
     * Find the best teacher for a subject based on keyword matching.
     */
    private function findBestTeacherForSubject(Subject $subject): array
    {
        $teachers = User::where('role', 'teacher')->get();
        $bestMatch = ['teacher' => null, 'score' => 0, 'matching_keywords' => []];

        $subjectKeywords = $this->extractKeywords($subject->keywords . ' ' . $subject->description);

        foreach ($teachers as $teacher) {
            $teacherKeywords = $this->extractKeywords($teacher->speciality ?? '');
            $matchResult = $this->calculateKeywordMatch($subjectKeywords, $teacherKeywords);

            if ($matchResult['score'] > $bestMatch['score']) {
                $bestMatch = [
                    'teacher' => $teacher,
                    'score' => $matchResult['score'],
                    'matching_keywords' => $matchResult['matching_keywords']
                ];
            }
        }

        return $bestMatch;
    }

    /**
     * Extract keywords from text.
     */
    private function extractKeywords(string $text): array
    {
        // Convert to lowercase and remove special characters
        $cleanText = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $text));

        // Split into words and filter out common words
        $words = array_filter(explode(' ', $cleanText), function($word) {
            return strlen(trim($word)) > 2 && !in_array(trim($word), [
                'the', 'and', 'for', 'are', 'but', 'not', 'you', 'all', 'can', 'had', 'her', 'was', 'one', 'our', 'out', 'day', 'get', 'has', 'him', 'his', 'how', 'man', 'new', 'now', 'old', 'see', 'two', 'way', 'who', 'boy', 'did', 'its', 'let', 'put', 'say', 'she', 'too', 'use'
            ]);
        });

        return array_unique(array_map('trim', $words));
    }

    /**
     * Calculate keyword matching score between subject and teacher.
     */
    private function calculateKeywordMatch(array $subjectKeywords, array $teacherKeywords): array
    {
        $matchingKeywords = array_intersect($subjectKeywords, $teacherKeywords);
        $score = 0;

        // Calculate score based on matches
        foreach ($matchingKeywords as $keyword) {
            // Longer keywords get higher weight
            $score += strlen($keyword) * 2;
        }

        // Bonus for number of matches
        $score += count($matchingKeywords) * 5;

        return [
            'score' => $score,
            'matching_keywords' => array_values($matchingKeywords)
        ];
    }

    /**
     * Assign teacher to subject.
     */
    private function assignTeacherToSubject(User $teacher, Subject $subject): void
    {
        $subject->update([
            'teacher_id' => $teacher->id
        ]);
    }

    /**
     * Get available teachers for manual assignment.
     * Returns teachers without showing student information.
     */
    public function getAvailableTeachers(): Collection
    {
        return User::where('role', 'teacher')
            ->select(['id', 'name', 'email', 'speciality', 'department'])
            ->get();
    }

    /**
     * Get external subjects available for teacher selection.
     * Returns subjects without student team information.
     */
    public function getExternalSubjectsForTeachers(): Collection
    {
        return Subject::where('is_external', true)
            ->where('status', 'validated')
            ->select(['id', 'title', 'description', 'keywords', 'tools', 'company_name'])
            ->get();
    }

    /**
     * Allow teacher to manually select/claim an external subject.
     */
    public function teacherSelectSubject(User $teacher, Subject $subject): array
    {
        if ($teacher->role !== 'teacher') {
            return ['success' => false, 'message' => 'Only teachers can select subjects'];
        }

        if (!$subject->is_external || $subject->status !== 'validated') {
            return ['success' => false, 'message' => 'Subject is not available for selection'];
        }

        if ($subject->teacher_id) {
            return ['success' => false, 'message' => 'Subject already assigned to another teacher'];
        }

        $this->assignTeacherToSubject($teacher, $subject);

        return [
            'success' => true,
            'message' => 'Subject successfully assigned',
            'subject' => $subject,
            'teacher' => $teacher
        ];
    }

    /**
     * Get teacher's assigned external subjects.
     */
    public function getTeacherAssignedSubjects(User $teacher): Collection
    {
        return Subject::where('is_external', true)
            ->where('teacher_id', $teacher->id)
            ->with(['teams' => function($query) {
                $query->select(['id', 'name', 'status', 'subject_id']);
            }])
            ->get();
    }

    /**
     * Get statistics for teacher assignments.
     */
    public function getAssignmentStatistics(): array
    {
        $totalExternal = Subject::where('is_external', true)
            ->where('status', 'validated')
            ->count();

        $assignedExternal = Subject::where('is_external', true)
            ->where('status', 'validated')
            ->whereNotNull('teacher_id')
            ->count();

        $totalTeachers = User::where('role', 'teacher')->count();

        $teachersWithAssignments = User::where('role', 'teacher')
            ->whereHas('subjects', function($query) {
                $query->where('is_external', true);
            })
            ->count();

        return [
            'total_external_subjects' => $totalExternal,
            'assigned_external_subjects' => $assignedExternal,
            'unassigned_external_subjects' => $totalExternal - $assignedExternal,
            'assignment_percentage' => $totalExternal > 0 ? round(($assignedExternal / $totalExternal) * 100, 2) : 0,
            'total_teachers' => $totalTeachers,
            'teachers_with_assignments' => $teachersWithAssignments
        ];
    }
}