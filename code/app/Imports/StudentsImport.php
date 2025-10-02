<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Speciality;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $specialityId;

    public function __construct($specialityId = null)
    {
        $this->specialityId = $specialityId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip rows with empty essential data
        if (empty($row['matricule']) || empty($row['nom']) || empty($row['prenom'])) {
            return null;
        }

        // Generate email from matricule if not provided
        $email = !empty($row['email']) ? $row['email'] : $row['matricule'] . '@student.university.edu';

        // Parse the name field if it contains both first and last name
        $fullName = trim($row['nom'] . ' ' . $row['prenom']);

        // Check if user exists by matricule or email
        $existingUser = User::where('matricule', $row['matricule'])
                          ->orWhere('email', $email)
                          ->first();

        $userData = [
            'name' => $fullName,
            'first_name' => $row['prenom'] ?? '',
            'last_name' => $row['nom'] ?? '',
            'email' => $email,
            'matricule' => $row['matricule'],
            'numero_inscription' => $row['numero_inscription'] ?? null,
            'annee_bac' => isset($row['annee_bac']) ? (int)$row['annee_bac'] : null,
            'section' => $row['section'] ?? 'Section_1',
            'groupe' => $row['groupe'] ?? 'G_01',
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
            'matricule' => 'required|string|max:50',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'numero_inscription' => 'nullable|string|max:100',
            'annee_bac' => 'nullable|integer|min:2000|max:' . (date('Y') + 5),
            'section' => 'nullable|string|max:50',
            'groupe' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
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
