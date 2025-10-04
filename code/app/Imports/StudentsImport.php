<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Speciality;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, WithStartRow, WithEvents
{
    protected $specialityId;
    protected $specialityInfo = [];

    public function __construct($specialityId = null)
    {
        $this->specialityId = $specialityId;
    }

    /**
     * Start reading from row 6 (data rows)
     */
    public function startRow(): int
    {
        return 6;
    }

    /**
     * Specify which row contains the headings
     */
    public function headingRow(): int
    {
        return 5;
    }

    /**
     * Register events to read metadata before processing
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Read metadata from the first 3 rows
                $this->specialityInfo = [
                    'program' => $sheet->getCell('A1')->getValue(),
                    'semester' => $sheet->getCell('A2')->getValue(),
                    'academic_year' => $sheet->getCell('A3')->getValue(),
                ];
            },
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Map the headers to correct field names (based on the Excel structure)
        // WithHeadingRow converts "Année Bac" to "annee_bac" and "Prénom" to "prenom"
        $mappedRow = [
            'numero_inscription' => isset($row['numero_inscription']) ? (string)$row['numero_inscription'] : null,
            'annee_bac' => $row['annee_bac'] ?? null,
            'matricule' => isset($row['matricule']) ? (string)$row['matricule'] : null,
            'nom' => $row['nom'] ?? null,
            'prenom' => $row['prenom'] ?? null,
            'section' => $row['section'] ?? 'Section_1',
            'groupe' => $row['groupe'] ?? 'G_01',
        ];

        // Skip rows with empty essential data
        if (empty($mappedRow['matricule']) || empty($mappedRow['nom']) || empty($mappedRow['prenom'])) {
            return null;
        }

        // Generate email from matricule if not provided
        $email = $mappedRow['matricule'] . '@student.university.edu';

        // Parse the name field
        $fullName = trim($mappedRow['nom'] . ' ' . $mappedRow['prenom']);

        // Check if user exists by matricule or numero_inscription
        $existingUser = User::where('matricule', $mappedRow['matricule'])
                          ->orWhere('numero_inscription', $mappedRow['numero_inscription'])
                          ->first();

        $userData = [
            'name' => $fullName,
            'first_name' => $mappedRow['prenom'],
            'last_name' => $mappedRow['nom'],
            'email' => $email,
            'matricule' => $mappedRow['matricule'],
            'numero_inscription' => $mappedRow['numero_inscription'],
            'annee_bac' => isset($mappedRow['annee_bac']) ? (int)$mappedRow['annee_bac'] : null,
            'section' => $mappedRow['section'],
            'groupe' => $mappedRow['groupe'],
            'role' => 'student',
            'status' => 'active',
            'speciality_id' => $this->specialityId,
            'academic_year' => date('Y') . '/' . (date('Y') + 1),
            'profile_completed' => true,
            'email_verified_at' => now(),
        ];

        if ($existingUser) {
            // Update existing user
            $existingUser->update($userData);
            return null; // Return null to avoid creating duplicate
        } else {
            // Create new user
            $userData['password'] = Hash::make('student123'); // Default password
            return new User($userData);
        }
    }

    /**
     * Validation rules for each row
     */
    public function rules(): array
    {
        return [
            'matricule' => 'required|max:50',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'numero_inscription' => 'nullable|max:100',
            'annee_bac' => 'nullable|integer|min:2000|max:' . (date('Y') + 5),
            'section' => 'nullable|string|max:50',
            'groupe' => 'nullable|string|max:50',
        ];
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages()
    {
        return [
            'matricule.required' => 'Le matricule est obligatoire.',
            'matricule.unique' => 'Ce matricule existe déjà.',
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.unique' => 'Cette adresse email existe déjà.',
            'annee_bac.integer' => 'L\'année du bac doit être un nombre.',
        ];
    }
}
