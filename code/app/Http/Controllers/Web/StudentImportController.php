<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportStudentsRequest;
use App\Models\User;
use App\Services\StudentImportService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use Spatie\Permission\Models\Role;

class StudentImportController extends Controller
{
    public function __construct(private StudentImportService $importService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin_pfe|chef_master');
    }

    /**
     * Show student import form
     */
    public function index(): View
    {
        $importHistory = $this->getImportHistory();
        $importStatistics = $this->getImportStatistics();
        $sampleData = $this->getSampleData();

        return view('pfe.admin.students.import.index', [
            'import_history' => $importHistory,
            'import_statistics' => $importStatistics,
            'sample_data' => $sampleData
        ]);
    }

    /**
     * Download sample Excel template
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $templatePath = $this->importService->generateTemplate();

        return response()->download($templatePath, 'student_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])->deleteFileAfterSend();
    }

    /**
     * Preview uploaded Excel file
     */
    public function preview(ImportStudentsRequest $request): View
    {
        $file = $request->file('excel_file');

        try {
            $previewData = $this->importService->previewFile($file);

            return view('pfe.admin.students.import.preview', [
                'preview_data' => $previewData,
                'file_info' => [
                    'name' => $file->getClientOriginalName(),
                    'size' => $this->formatFileSize($file->getSize()),
                    'rows_count' => count($previewData['students']),
                    'valid_rows' => count($previewData['valid_students']),
                    'invalid_rows' => count($previewData['errors'])
                ]
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['excel_file' => 'Error reading Excel file: ' . $e->getMessage()]);
        }
    }

    /**
     * Process and import students from Excel
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'update_existing' => 'boolean',
            'send_notifications' => 'boolean',
            'default_password_type' => 'required|in:generated,email_based,custom',
            'custom_password' => 'required_if:default_password_type,custom|min:8',
            'force_password_reset' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $options = [
                'update_existing' => $request->boolean('update_existing', false),
                'send_notifications' => $request->boolean('send_notifications', true),
                'default_password_type' => $request->input('default_password_type', 'generated'),
                'custom_password' => $request->input('custom_password'),
                'force_password_reset' => $request->boolean('force_password_reset', true)
            ];

            $result = $this->importService->importStudents($file, $options);

            // Log import activity
            $this->logImportActivity($result, $file->getClientOriginalName());

            DB::commit();

            $message = "Import completed successfully! ";
            $message .= "Created: {$result['created']}, ";
            $message .= "Updated: {$result['updated']}, ";
            $message .= "Skipped: {$result['skipped']}";

            if (!empty($result['errors'])) {
                $message .= ". Some rows had errors - check the import log for details.";
            }

            return redirect()->route('pfe.admin.students.import.index')
                ->with('success', $message)
                ->with('import_result', $result);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'import_error' => 'Import failed: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Show import results and errors
     */
    public function showResults(int $importId): View
    {
        $importLog = $this->getImportLog($importId);

        if (!$importLog) {
            abort(404, 'Import log not found');
        }

        return view('pfe.admin.students.import.results', [
            'import_log' => $importLog,
            'results' => $importLog['results'],
            'errors' => $importLog['errors'] ?? []
        ]);
    }

    /**
     * Export import errors to Excel
     */
    public function exportErrors(int $importId): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $importLog = $this->getImportLog($importId);

        if (!$importLog || empty($importLog['errors'])) {
            abort(404, 'No errors found for this import');
        }

        $filePath = $this->importService->exportErrors($importLog['errors']);

        return response()->download($filePath, "import_errors_{$importId}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])->deleteFileAfterSend();
    }

    /**
     * Bulk operations on imported students
     */
    public function bulkOperations(Request $request): RedirectResponse
    {
        $request->validate([
            'operation' => 'required|in:activate,deactivate,reset_passwords,send_welcome_emails',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id'
        ]);

        try {
            $result = $this->importService->performBulkOperation(
                $request->input('operation'),
                $request->input('student_ids')
            );

            return back()->with('success', $result['message']);

        } catch (\Exception $e) {
            return back()->withErrors(['operation_error' => $e->getMessage()]);
        }
    }

    /**
     * Validate Excel structure without importing
     */
    public function validateStructure(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $validation = $this->importService->validateFileStructure($request->file('excel_file'));

            return response()->json([
                'valid' => $validation['valid'],
                'errors' => $validation['errors'],
                'warnings' => $validation['warnings'] ?? [],
                'column_mapping' => $validation['column_mapping'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'errors' => ['File validation failed: ' . $e->getMessage()]
            ], 422);
        }
    }

    /**
     * Get import history
     */
    private function getImportHistory(): array
    {
        // In a real implementation, this would fetch from a student_imports table
        return [
            [
                'id' => 1,
                'filename' => 'students_2024_batch1.xlsx',
                'imported_at' => now()->subDays(7),
                'imported_by' => 'Admin User',
                'total_rows' => 150,
                'successful' => 147,
                'failed' => 3,
                'status' => 'completed'
            ],
            [
                'id' => 2,
                'filename' => 'students_2024_batch2.xlsx',
                'imported_at' => now()->subDays(2),
                'imported_by' => 'Chef Master',
                'total_rows' => 89,
                'successful' => 89,
                'failed' => 0,
                'status' => 'completed'
            ]
        ];
    }

    /**
     * Get import statistics
     */
    private function getImportStatistics(): array
    {
        $totalStudents = User::role('student')->count();
        $activeStudents = User::role('student')->where('is_active', true)->count();
        $recentImports = 5; // Would be calculated from imports table

        return [
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'inactive_students' => $totalStudents - $activeStudents,
            'recent_imports' => $recentImports,
            'last_import_date' => now()->subDays(2)->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get sample data for template
     */
    private function getSampleData(): array
    {
        return [
            'required_columns' => [
                'first_name' => 'Student\'s first name',
                'last_name' => 'Student\'s last name',
                'email' => 'Valid email address (must be unique)',
                'student_id' => 'Student ID number (must be unique)',
                'academic_year' => 'Academic year (e.g., 2024)',
                'specialization' => 'Student\'s specialization/major'
            ],
            'optional_columns' => [
                'phone' => 'Phone number',
                'date_of_birth' => 'Date of birth (YYYY-MM-DD format)',
                'address' => 'Student address',
                'emergency_contact' => 'Emergency contact information',
                'previous_education' => 'Previous education background',
                'skills' => 'Technical skills (comma-separated)',
                'interests' => 'Academic interests (comma-separated)'
            ],
            'validation_rules' => [
                'Email must be unique across all users',
                'Student ID must be unique',
                'Academic year must be a valid year',
                'Phone number should be in valid format',
                'Date of birth must be in YYYY-MM-DD format'
            ]
        ];
    }

    /**
     * Format file size for display
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Log import activity
     */
    private function logImportActivity(array $result, string $filename): void
    {
        // In a real implementation, this would log to a student_imports table
        \Log::info('Student import completed', [
            'filename' => $filename,
            'user_id' => auth()->id(),
            'results' => $result,
            'timestamp' => now()
        ]);
    }

    /**
     * Get import log by ID
     */
    private function getImportLog(int $importId): ?array
    {
        // In a real implementation, this would fetch from student_imports table
        // For now, return sample data
        if ($importId === 1) {
            return [
                'id' => 1,
                'filename' => 'students_2024_batch1.xlsx',
                'imported_at' => now()->subDays(7),
                'imported_by' => 'Admin User',
                'results' => [
                    'total_rows' => 150,
                    'created' => 147,
                    'updated' => 0,
                    'skipped' => 0,
                    'failed' => 3
                ],
                'errors' => [
                    ['row' => 15, 'error' => 'Email already exists: john.doe@example.com'],
                    ['row' => 23, 'error' => 'Invalid email format: invalid-email'],
                    ['row' => 89, 'error' => 'Student ID already exists: STU12345']
                ]
            ];
        }

        return null;
    }
}