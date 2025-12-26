<?php

namespace App\Services;

use App\Models\User;
use App\Models\Speciality;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Carbon\Carbon;

class StudentImportService
{
    protected array $results = [];
    protected array $errors = [];
    protected int $created = 0;
    protected int $updated = 0;
    protected int $skipped = 0;

    /**
     * Import students from Excel file
     */
    public function importFromExcel(string $filePath, array $specialityData): array
    {
        try {
            // Reset counters
            $this->resetCounters();

            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Get the highest row and column
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            Log::info("Excel file loaded: {$highestRow} rows, highest column: {$highestColumn}");

            // Create or get speciality
            $speciality = $this->createOrGetSpeciality($specialityData);

            // Extract metadata from first few rows (optional)
            $metadata = $this->extractMetadata($worksheet);

            // Start reading from row 4 (headers) and row 5 (data)
            $headers = $this->getHeaders($worksheet, 4);
            $columnMapping = $this->mapColumns($headers);

            Log::info('Column mapping:', $columnMapping);

            // Process student data starting from row 5
            DB::beginTransaction();

            try {
                for ($row = 5; $row <= $highestRow; $row++) {
                    $rowData = $this->getRowData($worksheet, $row, $columnMapping);

                    if ($this->isRowEmpty($rowData)) {
                        continue;
                    }

                    $this->processStudentRow($rowData, $speciality, $row);
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            return [
                'success' => true,
                'message' => "Import completed successfully",
                'data' => [
                    'created' => $this->created,
                    'updated' => $this->updated,
                    'skipped' => $this->skipped,
                    'total_processed' => $this->created + $this->updated + $this->skipped,
                    'speciality' => $speciality->toArray(),
                    'metadata' => $metadata,
                    'errors' => $this->errors
                ]
            ];

        } catch (Exception $e) {
            Log::error('Excel import error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error reading Excel file: ' . $e->getMessage(),
                'data' => []
            ];
        } catch (\Exception $e) {
            Log::error('General import error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Reset all counters
     */
    protected function resetCounters(): void
    {
        $this->created = 0;
        $this->updated = 0;
        $this->skipped = 0;
        $this->errors = [];
    }

    /**
     * Create or get existing speciality
     */
    protected function createOrGetSpeciality(array $data): Speciality
    {
        // If an existing speciality ID is provided, use it
        if (isset($data['existing_speciality_id'])) {
            return Speciality::findOrFail($data['existing_speciality_id']);
        }

        // Otherwise, create or find speciality
        return Speciality::firstOrCreate(
            [
                'name' => $data['name'],
                'level' => $data['level'],
                'academic_year' => $data['academic_year']
            ],
            [
                'code' => $data['code'] ?? null,
                'semester' => $data['semester'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => true
            ]
        );
    }

    /**
     * Extract metadata from top rows (rows 1-3)
     */
    protected function extractMetadata($worksheet): array
    {
        $metadata = [];

        try {
            // Try to extract program info from first few rows
            for ($row = 1; $row <= 3; $row++) {
                $cellValue = $worksheet->getCell("A{$row}")->getCalculatedValue();
                if (!empty($cellValue)) {
                    $metadata["row_{$row}"] = trim($cellValue);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not extract metadata: ' . $e->getMessage());
        }

        return $metadata;
    }

    /**
     * Get headers from specified row
     */
    protected function getHeaders($worksheet, int $headerRow): array
    {
        $headers = [];
        $highestColumn = $worksheet->getHighestColumn();
        $columnIterator = $worksheet->getColumnIterator('A', $highestColumn);

        foreach ($columnIterator as $column) {
            $columnIndex = $column->getColumnIndex();
            $cellValue = $worksheet->getCell($columnIndex . $headerRow)->getCalculatedValue();
            $headers[$columnIndex] = trim($cellValue);
        }

        return array_filter($headers); // Remove empty headers
    }

    /**
     * Map Excel columns to database fields
     */
    protected function mapColumns(array $headers): array
    {
        $mapping = [];

        foreach ($headers as $columnIndex => $headerName) {
            $normalizedHeader = $this->normalizeHeaderName($headerName);

            switch ($normalizedHeader) {
                case 'numero_inscription':
                case 'numero inscription':
                    $mapping[$columnIndex] = 'numero_inscription';
                    break;
                case 'matricule':
                    $mapping[$columnIndex] = 'matricule';
                    break;
                case 'nom':
                    $mapping[$columnIndex] = 'nom';
                    break;
                case 'prenom':
                case 'prénom':
                    $mapping[$columnIndex] = 'prenom';
                    break;
                case 'annee_bac':
                case 'année bac':
                case 'annee bac':
                    $mapping[$columnIndex] = 'annee_bac';
                    break;
                case 'date_naissance':
                case 'date de naissance':
                    $mapping[$columnIndex] = 'date_naissance';
                    break;
                case 'section':
                    $mapping[$columnIndex] = 'section';
                    break;
                case 'groupe':
                    $mapping[$columnIndex] = 'groupe';
                    break;
                case 'absent':
                case 'absent ?':
                    $mapping[$columnIndex] = 'absent';
                    break;
            }
        }

        return $mapping;
    }

    /**
     * Normalize header name for comparison
     */
    protected function normalizeHeaderName(string $header): string
    {
        return strtolower(trim(str_replace(['é', 'è', 'à', 'ç'], ['e', 'e', 'a', 'c'], $header)));
    }

    /**
     * Get row data based on column mapping
     */
    protected function getRowData($worksheet, int $row, array $columnMapping): array
    {
        $data = [];

        foreach ($columnMapping as $columnIndex => $fieldName) {
            $cellValue = $worksheet->getCell($columnIndex . $row)->getCalculatedValue();
            $data[$fieldName] = $this->cleanCellValue($cellValue);
        }

        return $data;
    }

    /**
     * Clean cell value
     */
    protected function cleanCellValue($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return trim((string) $value);
    }

    /**
     * Check if row is empty
     */
    protected function isRowEmpty(array $rowData): bool
    {
        $requiredFields = ['numero_inscription', 'nom', 'prenom'];

        foreach ($requiredFields as $field) {
            if (!empty($rowData[$field] ?? null)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Process a single student row
     */
    protected function processStudentRow(array $rowData, Speciality $speciality, int $rowNumber): void
    {
        try {
            // Validate required fields
            $validation = $this->validateStudentData($rowData, $rowNumber);
            if (!$validation['valid']) {
                $this->errors[] = $validation['error'];
                $this->skipped++;
                return;
            }

            // Prepare user data
            $userData = $this->prepareUserData($rowData, $speciality);

            // Find existing user by numero_inscription or matricule
            $existingUser = $this->findExistingUser($rowData);

            if ($existingUser) {
                // Update existing user but preserve critical fields if they exist
                $updateData = $userData;

                // Preserve existing numero_inscription if the current one is empty
                if (empty($updateData['numero_inscription']) && !empty($existingUser->numero_inscription)) {
                    $updateData['numero_inscription'] = $existingUser->numero_inscription;
                }

                // Preserve existing matricule if the current one is empty
                if (empty($updateData['matricule']) && !empty($existingUser->matricule)) {
                    $updateData['matricule'] = $existingUser->matricule;
                }

                // Don't update email if it already exists and is different
                if ($existingUser->email !== $userData['email']) {
                    $updateData['email'] = $existingUser->email;
                }

                $existingUser->update($updateData);
                $this->updated++;
                Log::info("Updated user: {$existingUser->email} -> {$userData['name']} (Row {$rowNumber})");
            } else {
                // Double-check for email uniqueness before creating
                $emailExists = User::where('email', $userData['email'])->exists();
                if ($emailExists) {
                    // Generate alternative email
                    $userData['email'] = $this->generateUniqueEmail($userData['email']);
                }

                User::create($userData);
                $this->created++;
                Log::info("Created user: {$userData['email']} (Row {$rowNumber})");
            }

        } catch (\Exception $e) {
            $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
            $this->skipped++;
            Log::error("Error processing row {$rowNumber}: " . $e->getMessage());
        }
    }

    /**
     * Validate student data
     */
    protected function validateStudentData(array $data, int $rowNumber): array
    {
        $errors = [];

        if (empty($data['numero_inscription'] ?? null)) {
            $errors[] = 'Numero Inscription is required';
        }

        if (empty($data['nom'] ?? null)) {
            $errors[] = 'Nom is required';
        }

        if (empty($data['prenom'] ?? null)) {
            $errors[] = 'Prénom is required';
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'error' => "Row {$rowNumber}: " . implode(', ', $errors)
            ];
        }

        return ['valid' => true];
    }

    /**
     * Prepare user data for database
     */
    protected function prepareUserData(array $rowData, Speciality $speciality): array
    {
        $nom = $rowData['nom'] ?? '';
        $prenom = $rowData['prenom'] ?? '';
        $numeroInscription = $rowData['numero_inscription'] ?? '';

        // Generate email from name and numero inscription
        $email = $this->generateEmail($prenom, $nom, $numeroInscription);

        $userData = [
            'name' => trim($prenom . ' ' . $nom),
            'email' => $email,
            'password' => Hash::make('password123'), // Default password
            'role' => 'student',
            'speciality_id' => $speciality->id,
            'department' => 'Computer Science', // Fixed department as requested
            'numero_inscription' => $numeroInscription,
            'matricule' => $rowData['matricule'] ?? null,
            'annee_bac' => $this->parseYear($rowData['annee_bac'] ?? null),
            'date_naissance' => $this->parseDate($rowData['date_naissance'] ?? null),
            'section' => $rowData['section'] ?? null,
            'groupe' => $rowData['groupe'] ?? null,
            'email_verified_at' => now(),
        ];

        return $userData;
    }

    /**
     * Generate email for student
     */
    protected function generateEmail(string $prenom, string $nom, string $numeroInscription): string
    {
        $cleanPrenom = $this->cleanForEmail($prenom);
        $cleanNom = $this->cleanForEmail($nom);

        return strtolower($cleanPrenom . '.' . $cleanNom . '.' . $numeroInscription . '@student.university.edu');
    }

    /**
     * Clean string for email generation
     */
    protected function cleanForEmail(string $str): string
    {
        // Remove accents and special characters
        $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        // Keep only letters and numbers
        $str = preg_replace('/[^a-zA-Z0-9]/', '', $str);
        return strtolower($str);
    }

    /**
     * Generate unique email by adding suffix if email already exists
     */
    protected function generateUniqueEmail(string $baseEmail): string
    {
        $counter = 1;
        $emailParts = explode('@', $baseEmail);
        $localPart = $emailParts[0];
        $domain = $emailParts[1];

        while (User::where('email', $baseEmail)->exists() && $counter <= 999) {
            $baseEmail = $localPart . $counter . '@' . $domain;
            $counter++;
        }

        return $baseEmail;
    }

    /**
     * Find existing user
     */
    protected function findExistingUser(array $rowData): ?User
    {
        $numeroInscription = $rowData['numero_inscription'] ?? null;
        $matricule = $rowData['matricule'] ?? null;
        $nom = $rowData['nom'] ?? '';
        $prenom = $rowData['prenom'] ?? '';

        // Priority 1: Find by numero_inscription (most reliable)
        if ($numeroInscription) {
            $user = User::where('numero_inscription', $numeroInscription)->first();
            if ($user) return $user;
        }

        // Priority 2: Find by matricule
        if ($matricule) {
            $user = User::where('matricule', $matricule)->first();
            if ($user) return $user;
        }

        // Priority 3: Find by generated email (prevent email duplicates)
        if ($numeroInscription && $nom && $prenom) {
            $email = $this->generateEmail($prenom, $nom, $numeroInscription);
            $user = User::where('email', $email)->first();
            if ($user) return $user;
        }

        // Priority 4: Find by name combination (as last resort)
        if ($nom && $prenom) {
            $fullName = trim($prenom . ' ' . $nom);
            $user = User::where('name', $fullName)
                        ->where('role', 'student')
                        ->first();
            if ($user) return $user;
        }

        return null;
    }

    /**
     * Parse year from various formats
     */
    protected function parseYear($value): ?int
    {
        if (empty($value)) return null;

        $year = (int) $value;

        // Validate year range
        if ($year >= 1950 && $year <= date('Y') + 10) {
            return $year;
        }

        return null;
    }

    /**
     * Parse date from various formats
     */
    protected function parseDate($value): ?string
    {
        if (empty($value)) return null;

        try {
            $date = Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Could not parse date: {$value}");
            return null;
        }
    }
}