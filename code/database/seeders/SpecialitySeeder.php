<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Speciality;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentAcademicYear = Speciality::getCurrentAcademicYear();

        // License specialities (existing ones if any)
        $licenceSpecialities = [
            [
                'name' => 'Informatique',
                'code' => 'INF',
                'level' => 'licence',
                'academic_year' => $currentAcademicYear,
                'semester' => 'S6',
                'description' => 'Licence en Informatique',
                'is_active' => true,
            ],
            [
                'name' => 'Systèmes d\'Information',
                'code' => 'SI',
                'level' => 'licence',
                'academic_year' => $currentAcademicYear,
                'semester' => 'S6',
                'description' => 'Licence en Systèmes d\'Information',
                'is_active' => true,
            ],
        ];

        // Master specialities as requested
        $masterSpecialities = [
            [
                'name' => 'Informatique et Technologies de l\'Information',
                'code' => 'ILTI',
                'level' => 'master',
                'academic_year' => $currentAcademicYear,
                'semester' => 'S4',
                'description' => 'Master en Informatique et Technologies de l\'Information',
                'is_active' => true,
            ],
            [
                'name' => 'Systèmes d\'Information et Réseaux',
                'code' => 'SIR',
                'level' => 'master',
                'academic_year' => $currentAcademicYear,
                'semester' => 'S4',
                'description' => 'Master en Systèmes d\'Information et Réseaux',
                'is_active' => true,
            ],
            [
                'name' => 'Technologies de l\'Information',
                'code' => 'TI',
                'level' => 'master',
                'academic_year' => $currentAcademicYear,
                'semester' => 'S4',
                'description' => 'Master en Technologies de l\'Information',
                'is_active' => true,
            ],
            [
                'name' => 'Intelligence Artificielle et Applications',
                'code' => 'I2A',
                'level' => 'master',
                'academic_year' => $currentAcademicYear,
                'semester' => 'S4',
                'description' => 'Master en Intelligence Artificielle et Applications',
                'is_active' => true,
            ],
            [
                'name' => 'Génie Logiciel',
                'code' => 'GL',
                'level' => 'master',
                'academic_year' => $currentAcademicYear,
                'semester' => 'S4',
                'description' => 'Master en Génie Logiciel',
                'is_active' => true,
            ],
            [
                'name' => 'Génie Logiciel - Ingénierie',
                'code' => 'GL-ING',
                'level' => 'master',
                'academic_year' => $currentAcademicYear,
                'semester' => 'S4',
                'description' => 'Master en Génie Logiciel - Ingénierie',
                'is_active' => true,
            ],
        ];

        // Combine all specialities
        $allSpecialities = array_merge($licenceSpecialities, $masterSpecialities);

        // Create specialities, avoiding duplicates
        foreach ($allSpecialities as $specialityData) {
            Speciality::firstOrCreate(
                [
                    'code' => $specialityData['code'],
                    'level' => $specialityData['level'],
                    'academic_year' => $specialityData['academic_year'],
                ],
                $specialityData
            );
        }

        $this->command->info('Created ' . count($allSpecialities) . ' specialities successfully.');
        $this->command->info('Master specialities added: ILTI, SIR, TI, I2A, GL, GL-ING');
    }
}
