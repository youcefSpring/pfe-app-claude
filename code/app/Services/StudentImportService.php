<?php

namespace App\Services;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Spatie\Permission\Models\Role;

class StudentImportService
{
    private array $requiredColumns = [
        'first_name', 'last_name', 'email', 'student_id', 'academic_year', 'specialization'
    ];

    private array $optionalColumns = [
        'phone', 'date_of_birth', 'address', 'emergency_contact',
        'previous_education', 'skills', 'interests'
    ];

    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * Generate Excel template for student import
     */
    public function generateTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set up headers
        $headers = array_merge($this->requiredColumns, $this->optionalColumns);
        $columnIndex = 1;

        foreach ($headers as $header) {
            $cellCoordinate = $this->numberToLetter($columnIndex) . '1';
            $sheet->setCellValue($cellCoordinate, ucfirst(str_replace('_', ' ', $header)));

            // Style required columns differently
            if (in_array($header, $this->requiredColumns)) {
                $sheet->getStyle($cellCoordinate)->getFont()->setBold(true);
                $sheet->getStyle($cellCoordinate)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFE6E6');
            } else {
                $sheet->getStyle($cellCoordinate)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('E6F3FF');
            }

            $columnIndex++;
        }

        // Add sample data
        $sampleData = [
            [
                'John', 'Doe', 'john.doe@example.com', 'STU2024001', '2024', 'Computer Science',
                '+1234567890', '1995-05-15', '123 Main St, City', 'Jane Doe: +0987654321',
                'Bachelor in Information Technology', 'PHP,Laravel,JavaScript', 'Web Development,AI'
            ],
            [
                'Jane', 'Smith', 'jane.smith@example.com', 'STU2024002', '2024', 'Software Engineering',
                '+1234567891', '1996-08-22', '456 Oak Ave, City', 'Bob Smith: +0987654322',
                'Bachelor in Computer Engineering', 'Python,Django,React', 'Machine Learning,Data Science'
            ]
        ];

        $rowIndex = 2;
        foreach ($sampleData as $row) {
            $columnIndex = 1;
            foreach ($row as $value) {
                $cellCoordinate = $this->numberToLetter($columnIndex) . $rowIndex;
                $sheet->setCellValue($cellCoordinate, $value);
                $columnIndex++;
            }
            $rowIndex++;
        }

        // Auto-size columns
        foreach (range('A', $this->numberToLetter(count($headers))) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add instructions sheet
        $instructionsSheet = $spreadsheet->createSheet();
        $instructionsSheet->setTitle('Instructions');
        $this->addInstructions($instructionsSheet);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Students');

        // Save to temporary file
        $filename = 'student_import_template_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);

        // Ensure temp directory exists
        Storage::disk('local')->makeDirectory('temp');

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * Preview Excel file content
     */
    public function previewFile(UploadedFile $file): array
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        if (empty($data)) {
            throw new \Exception('The Excel file is empty');
        }

        $headers = array_shift($data);
        $normalizedHeaders = $this->normalizeHeaders($headers);

        // Validate structure
        $structureValidation = $this->validateStructure($normalizedHeaders);
        if (!$structureValidation['valid']) {
            throw new \Exception('Invalid file structure: ' . implode(', ', $structureValidation['errors']));
        }

        $students = [];
        $validStudents = [];
        $errors = [];

        foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // +2 because we removed headers and Excel is 1-indexed

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $studentData = $this->mapRowToStudentData($row, $normalizedHeaders);
            $validation = $this->validateStudentData($studentData, $rowNumber);

            $students[] = [
                'row_number' => $rowNumber,
                'data' => $studentData,
                'valid' => $validation['valid'],
                'errors' => $validation['errors']
            ];

            if ($validation['valid']) {
                $validStudents[] = $studentData;
            } else {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validation['errors'],
                    'data' => $studentData
                ];
            }
        }

        return [
            'students' => $students,
            'valid_students' => $validStudents,
            'errors' => $errors,
            'headers' => $normalizedHeaders,
            'total_rows' => count($students)
        ];
    }

    /**
     * Import students from Excel file
     */
    public function importStudents(UploadedFile $file, array $options = []): array
    {
        $previewData = $this->previewFile($file);

        if (empty($previewData['valid_students'])) {
            throw new \Exception('No valid students found in the file');
        }

        $result = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        $studentRole = Role::where('name', 'student')->first();
        if (!$studentRole) {
            throw new \Exception('Student role not found in the system');
        }

        foreach ($previewData['valid_students'] as $studentData) {
            try {
                $existingUser = User::where('email', $studentData['email'])
                    ->orWhere('student_id', $studentData['student_id'])
                    ->first();

                if ($existingUser) {
                    if ($options['update_existing'] ?? false) {
                        $this->updateExistingStudent($existingUser, $studentData);
                        $result['updated']++;
                    } else {
                        $result['skipped']++;
                    }
                } else {
                    $user = $this->createNewStudent($studentData, $options);
                    $user->assignRole($studentRole);
                    $result['created']++;

                    // Send welcome notification if enabled
                    if ($options['send_notifications'] ?? true) {
                        $this->sendWelcomeNotification($user, $options);
                    }
                }

            } catch (\Exception $e) {
                $result['errors'][] = [
                    'student' => $studentData['email'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return $result;
    }

    /**
     * Validate file structure
     */
    public function validateFileStructure(UploadedFile $file): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            if (empty($data)) {
                return ['valid' => false, 'errors' => ['File is empty']];
            }

            $headers = $data[0];
            $normalizedHeaders = $this->normalizeHeaders($headers);

            return $this->validateStructure($normalizedHeaders);

        } catch (\Exception $e) {
            return ['valid' => false, 'errors' => ['Failed to read file: ' . $e->getMessage()]];
        }
    }

    /**
     * Export errors to Excel file
     */
    public function exportErrors(array $errors): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['Row Number', 'Email', 'Student ID', 'Errors'];
        $columnIndex = 1;
        foreach ($headers as $header) {
            $cellCoordinate = $this->numberToLetter($columnIndex) . '1';
            $sheet->setCellValue($cellCoordinate, $header);
            $sheet->getStyle($cellCoordinate)->getFont()->setBold(true);
            $columnIndex++;
        }

        // Error data
        $rowIndex = 2;
        foreach ($errors as $error) {
            $sheet->setCellValue('A' . $rowIndex, $error['row']);
            $sheet->setCellValue('B' . $rowIndex, $error['data']['email'] ?? '');
            $sheet->setCellValue('C' . $rowIndex, $error['data']['student_id'] ?? '');
            $sheet->setCellValue('D' . $rowIndex, implode('; ', $error['errors']));
            $rowIndex++;
        }

        // Auto-size columns
        foreach (['A', 'B', 'C', 'D'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Save to temporary file
        $filename = 'import_errors_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * Perform bulk operations on students
     */
    public function performBulkOperation(string $operation, array $studentIds): array
    {
        $students = User::whereIn('id', $studentIds)->get();

        switch ($operation) {
            case 'activate':
                $students->each(fn($student) => $student->update(['is_active' => true]));
                return ['message' => 'Students activated successfully'];

            case 'deactivate':
                $students->each(fn($student) => $student->update(['is_active' => false]));
                return ['message' => 'Students deactivated successfully'];

            case 'reset_passwords':
                $count = 0;
                foreach ($students as $student) {
                    $newPassword = Str::random(12);
                    $student->update([
                        'password' => Hash::make($newPassword),
                        'must_change_password' => true
                    ]);

                    // Send password reset notification
                    $this->notificationService->notify(
                        $student,
                        'password_reset',
                        'Password Reset',
                        "Your password has been reset. New password: {$newPassword}",
                        ['new_password' => $newPassword]
                    );
                    $count++;
                }
                return ['message' => "Passwords reset for {$count} students"];

            case 'send_welcome_emails':
                foreach ($students as $student) {
                    $this->notificationService->notify(
                        $student,
                        'welcome',
                        'Welcome to PFE Platform',
                        'Welcome to the PFE platform. Please log in to get started.',
                        ['login_url' => route('login')]
                    );
                }
                return ['message' => 'Welcome emails sent successfully'];

            default:
                throw new \Exception('Invalid operation');
        }
    }

    // Private helper methods

    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            return strtolower(str_replace([' ', '-'], '_', trim($header)));
        }, $headers);
    }

    private function validateStructure(array $headers): array
    {
        $missingRequired = array_diff($this->requiredColumns, $headers);

        if (!empty($missingRequired)) {
            return [
                'valid' => false,
                'errors' => ['Missing required columns: ' . implode(', ', $missingRequired)]
            ];
        }

        return ['valid' => true, 'errors' => []];
    }

    private function isEmptyRow(array $row): bool
    {
        return empty(array_filter($row, fn($value) => !is_null($value) && trim($value) !== ''));
    }

    private function mapRowToStudentData(array $row, array $headers): array
    {
        $studentData = [];

        foreach ($headers as $index => $header) {
            if (isset($row[$index])) {
                $studentData[$header] = trim($row[$index]);
            }
        }

        return $studentData;
    }

    private function validateStudentData(array $data, int $rowNumber): array
    {
        $errors = [];

        // Required field validation
        foreach ($this->requiredColumns as $column) {
            if (empty($data[$column])) {
                $errors[] = "Missing required field: {$column}";
            }
        }

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        // Check for existing email
        if (!empty($data['email']) && User::where('email', $data['email'])->exists()) {
            $errors[] = 'Email already exists in system';
        }

        // Check for existing student ID
        if (!empty($data['student_id']) && User::where('student_id', $data['student_id'])->exists()) {
            $errors[] = 'Student ID already exists in system';
        }

        // Academic year validation
        if (!empty($data['academic_year']) && !is_numeric($data['academic_year'])) {
            $errors[] = 'Academic year must be a number';
        }

        // Date of birth validation
        if (!empty($data['date_of_birth']) && !strtotime($data['date_of_birth'])) {
            $errors[] = 'Invalid date of birth format (use YYYY-MM-DD)';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    private function createNewStudent(array $data, array $options): User
    {
        // Generate password based on options
        $password = match ($options['default_password_type'] ?? 'generated') {
            'email_based' => substr($data['email'], 0, strpos($data['email'], '@')),
            'custom' => $options['custom_password'],
            default => Str::random(12)
        };

        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'student_id' => $data['student_id'],
            'password' => Hash::make($password),
            'academic_year' => $data['academic_year'],
            'specialization' => $data['specialization'],
            'phone' => $data['phone'] ?? null,
            'date_of_birth' => !empty($data['date_of_birth']) ? date('Y-m-d', strtotime($data['date_of_birth'])) : null,
            'address' => $data['address'] ?? null,
            'emergency_contact' => $data['emergency_contact'] ?? null,
            'previous_education' => $data['previous_education'] ?? null,
            'skills' => !empty($data['skills']) ? explode(',', $data['skills']) : null,
            'interests' => !empty($data['interests']) ? explode(',', $data['interests']) : null,
            'is_active' => true,
            'must_change_password' => $options['force_password_reset'] ?? true,
            'imported_at' => now(),
            'imported_by' => auth()->id()
        ]);
    }

    private function updateExistingStudent(User $user, array $data): void
    {
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'academic_year' => $data['academic_year'],
            'specialization' => $data['specialization'],
            'phone' => $data['phone'] ?? $user->phone,
            'address' => $data['address'] ?? $user->address,
            'emergency_contact' => $data['emergency_contact'] ?? $user->emergency_contact,
            'previous_education' => $data['previous_education'] ?? $user->previous_education,
            'skills' => !empty($data['skills']) ? explode(',', $data['skills']) : $user->skills,
            'interests' => !empty($data['interests']) ? explode(',', $data['interests']) : $user->interests,
            'updated_via_import' => true,
            'last_import_update' => now()
        ]);
    }

    private function sendWelcomeNotification(User $user, array $options): void
    {
        $this->notificationService->notify(
            $user,
            'student_imported',
            'Welcome to PFE Platform',
            'Your account has been created. Please log in and change your password.',
            [
                'login_url' => route('login'),
                'student_id' => $user->student_id,
                'must_change_password' => $options['force_password_reset'] ?? true
            ]
        );
    }

    private function addInstructions($sheet): void
    {
        $instructions = [
            'Student Import Instructions',
            '',
            'Required Columns (highlighted in red):',
            '• first_name: Student\'s first name',
            '• last_name: Student\'s last name',
            '• email: Valid email address (must be unique)',
            '• student_id: Student ID number (must be unique)',
            '• academic_year: Academic year (e.g., 2024)',
            '• specialization: Student\'s specialization/major',
            '',
            'Optional Columns (highlighted in blue):',
            '• phone: Phone number',
            '• date_of_birth: Date of birth (YYYY-MM-DD format)',
            '• address: Student address',
            '• emergency_contact: Emergency contact information',
            '• previous_education: Previous education background',
            '• skills: Technical skills (comma-separated)',
            '• interests: Academic interests (comma-separated)',
            '',
            'Important Notes:',
            '• Do not modify the header row',
            '• Email addresses must be unique',
            '• Student IDs must be unique',
            '• Use YYYY-MM-DD format for dates',
            '• Separate multiple skills/interests with commas',
            '• Remove sample data before importing'
        ];

        foreach ($instructions as $index => $instruction) {
            $sheet->setCellValue('A' . ($index + 1), $instruction);
            if ($index === 0) {
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            }
        }

        $sheet->getColumnDimension('A')->setWidth(50);
    }

    private function numberToLetter(int $number): string
    {
        $letter = '';
        while ($number > 0) {
            $number--;
            $letter = chr(65 + ($number % 26)) . $letter;
            $number = intval($number / 26);
        }
        return $letter;
    }
}