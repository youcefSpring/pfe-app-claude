<?php

namespace App\Services;

use App\Models\Defense;
use App\Models\PfeProject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PvGenerationService
{
    /**
     * Generate PV document for defense
     */
    public function generateDefensePv(Defense $defense): string
    {
        if ($defense->status !== 'completed') {
            throw new \InvalidArgumentException('Defense must be completed to generate PV');
        }

        $data = $this->preparePvData($defense);
        $pdf = Pdf::loadView('pdf.defense-pv', $data);

        $filename = $this->generatePvFilename($defense);
        $filepath = "pvs/{$filename}";

        Storage::disk('local')->put($filepath, $pdf->output());

        // Update defense with PV file path
        $defense->update([
            'pv_file_path' => $filepath,
            'pv_generated_at' => now()
        ]);

        return $filepath;
    }

    /**
     * Prepare data for PV template
     */
    private function preparePvData(Defense $defense): array
    {
        $project = $defense->project;
        $team = $project->team;
        $subject = $project->subject;

        return [
            'defense' => $defense,
            'project' => $project,
            'team' => $team,
            'subject' => $subject,
            'student_info' => $this->getStudentInfo($team),
            'jury_info' => $this->getJuryInfo($defense),
            'academic_year' => $this->getCurrentAcademicYear(),
            'institution_info' => $this->getInstitutionInfo(),
            'grades' => $this->formatGrades($defense),
            'generated_at' => now(),
            'generated_by' => auth()->user()
        ];
    }

    /**
     * Get formatted student information
     */
    private function getStudentInfo($team): array
    {
        return $team->members->map(function ($member) {
            $user = $member->user;
            return [
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'student_id' => $user->student_id,
                'email' => $user->email,
                'department' => $user->department,
                'role_in_team' => $member->role ?? ($member->id === $team->leader_id ? 'Team Leader' : 'Member')
            ];
        })->toArray();
    }

    /**
     * Get formatted jury information
     */
    private function getJuryInfo(Defense $defense): array
    {
        return [
            'president' => [
                'name' => $defense->juryPresident->first_name . ' ' . $defense->juryPresident->last_name,
                'title' => $defense->juryPresident->title ?? 'Professor',
                'department' => $defense->juryPresident->department,
                'signature_placeholder' => true
            ],
            'examiner' => [
                'name' => $defense->juryExaminer->first_name . ' ' . $defense->juryExaminer->last_name,
                'title' => $defense->juryExaminer->title ?? 'Professor',
                'department' => $defense->juryExaminer->department,
                'signature_placeholder' => true
            ],
            'supervisor' => [
                'name' => $defense->jurySupervisor->first_name . ' ' . $defense->jurySupervisor->last_name,
                'title' => $defense->jurySupervisor->title ?? 'Professor',
                'department' => $defense->jurySupervisor->department,
                'signature_placeholder' => true
            ]
        ];
    }

    /**
     * Format grades for display
     */
    private function formatGrades(Defense $defense): array
    {
        $grades = json_decode($defense->grades, true) ?? [];

        return [
            'technical_grade' => $grades['technical'] ?? null,
            'presentation_grade' => $grades['presentation'] ?? null,
            'report_grade' => $grades['report'] ?? null,
            'final_grade' => $grades['final'] ?? null,
            'final_grade_letter' => $this->convertToLetterGrade($grades['final'] ?? 0),
            'mention' => $this->getMention($grades['final'] ?? 0),
            'comments' => $grades['comments'] ?? ''
        ];
    }

    /**
     * Convert numerical grade to letter grade
     */
    private function convertToLetterGrade(float $grade): string
    {
        if ($grade >= 18) return 'A+';
        if ($grade >= 16) return 'A';
        if ($grade >= 14) return 'B+';
        if ($grade >= 12) return 'B';
        if ($grade >= 10) return 'C';
        return 'F';
    }

    /**
     * Get mention based on grade
     */
    private function getMention(float $grade): string
    {
        if ($grade >= 18) return 'Excellent';
        if ($grade >= 16) return 'Très Bien';
        if ($grade >= 14) return 'Bien';
        if ($grade >= 12) return 'Assez Bien';
        if ($grade >= 10) return 'Passable';
        return 'Insuffisant';
    }

    /**
     * Get current academic year
     */
    private function getCurrentAcademicYear(): string
    {
        $currentYear = Carbon::now()->year;
        $startYear = Carbon::now()->month >= 9 ? $currentYear : $currentYear - 1;
        $endYear = $startYear + 1;

        return "{$startYear}-{$endYear}";
    }

    /**
     * Get institution information (configurable)
     */
    private function getInstitutionInfo(): array
    {
        return [
            'name' => config('app.institution_name', 'University Name'),
            'faculty' => config('app.faculty_name', 'Faculty of Computer Science'),
            'department' => config('app.department_name', 'Department of Computer Science'),
            'address' => config('app.institution_address', 'University Address'),
            'logo_path' => config('app.institution_logo', 'images/university-logo.png')
        ];
    }

    /**
     * Generate standardized PV filename
     */
    private function generatePvFilename(Defense $defense): string
    {
        $date = $defense->defense_date->format('Y-m-d');
        $projectId = $defense->project->id;
        $teamName = str_replace(' ', '_', $defense->project->team->name);

        return "PV_Defense_{$projectId}_{$teamName}_{$date}.pdf";
    }

    /**
     * Generate bulk PV documents for multiple defenses
     */
    public function generateBulkPvs(array $defenseIds): array
    {
        $results = [];

        foreach ($defenseIds as $defenseId) {
            try {
                $defense = Defense::findOrFail($defenseId);
                $filepath = $this->generateDefensePv($defense);
                $results[$defenseId] = [
                    'success' => true,
                    'filepath' => $filepath,
                    'filename' => basename($filepath)
                ];
            } catch (\Exception $e) {
                $results[$defenseId] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Generate archive of multiple PV documents
     */
    public function generatePvArchive(array $defenseIds): string
    {
        $zip = new \ZipArchive();
        $archiveName = 'PV_Archive_' . now()->format('Y-m-d_H-i-s') . '.zip';
        $archivePath = storage_path("app/temp/{$archiveName}");

        if ($zip->open($archivePath, \ZipArchive::CREATE) === TRUE) {
            foreach ($defenseIds as $defenseId) {
                $defense = Defense::find($defenseId);
                if ($defense && $defense->pv_file_path) {
                    $pvPath = storage_path("app/{$defense->pv_file_path}");
                    if (file_exists($pvPath)) {
                        $zip->addFile($pvPath, basename($defense->pv_file_path));
                    }
                }
            }
            $zip->close();
        }

        return $archivePath;
    }

    /**
     * Validate PV data before generation
     */
    public function validatePvData(Defense $defense): array
    {
        $errors = [];

        if (!$defense->defense_date) {
            $errors[] = 'Defense date is required';
        }

        if (!$defense->room_id) {
            $errors[] = 'Defense room is required';
        }

        if (!$defense->jury_president_id || !$defense->jury_examiner_id || !$defense->jury_supervisor_id) {
            $errors[] = 'Complete jury assignment is required';
        }

        if (!$defense->grades) {
            $errors[] = 'Defense grades are required';
        }

        if ($defense->status !== 'completed') {
            $errors[] = 'Defense must be completed before generating PV';
        }

        return $errors;
    }

    /**
     * Get PV template preview data
     */
    public function getPvPreviewData(Defense $defense): array
    {
        return $this->preparePvData($defense);
    }
}