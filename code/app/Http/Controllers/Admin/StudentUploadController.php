<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\Speciality;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class StudentUploadController extends Controller
{
    /**
     * Show the student upload form.
     */
    public function showUploadForm()
    {
        $specialities = Speciality::all();
        return view('admin.students.upload', compact('specialities'));
    }

    /**
     * Handle the Excel file upload and import.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
            'speciality_id' => 'required|exists:specialities,id',
        ]);

        try {
            DB::beginTransaction();

            $speciality = Speciality::findOrFail($request->speciality_id);
            $initialStudentCount = DB::table('users')->where('role', 'student')->count();

            // Import the Excel file
            $import = new StudentsImport($request->speciality_id);
            Excel::import($import, $request->file('excel_file'));

            DB::commit();

            $finalStudentCount = DB::table('users')->where('role', 'student')->count();
            $importedCount = $finalStudentCount - $initialStudentCount;

            // Prepare detailed import results
            $importDetails = [
                'created' => $importedCount,
                'updated' => 0, // For now, we'll implement this tracking later
                'skipped' => 0,
                'total_processed' => $importedCount,
                'errors' => [],
                'speciality' => [
                    'name' => $speciality->name,
                    'level' => $speciality->level,
                    'academic_year' => $speciality->academic_year,
                    'semester' => $speciality->semester
                ]
            ];

            return redirect()->back()
                ->with('success', "✅ Import réussi! {$importedCount} étudiants ont été importés pour la spécialité '{$speciality->name}'.")
                ->with('import_details', $importDetails);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();

            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Ligne {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->back()
                ->withErrors(['excel_file' => 'Erreurs de validation:'])
                ->with('validation_errors', $errorMessages);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors([
                'excel_file' => 'Erreur lors de l\'import: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download a sample Excel template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'numero_inscription',
            'annee_bac',
            'matricule',
            'nom',
            'prenom',
            'section',
            'groupe',
            'email'
        ];

        $sampleData = [
            [
                'UN35012025202031025123',
                '2020',
                '31025107',
                'KACED',
                'NASSIM TAHA',
                'Section_1',
                'G_02',
                'nassim.kaced@student.university.edu'
            ],
            [
                'UN35012025212131066540',
                '2021',
                '31066540',
                'BENTCHAKAL',
                'FATIMA YASMIN',
                'Section_1',
                'G_02',
                'fatima.bentchakal@student.university.edu'
            ]
        ];

        $filename = 'template_import_etudiants.xlsx';

        return Excel::download(new class($headers, $sampleData) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $headers;
            private $data;

            public function __construct($headers, $data)
            {
                $this->headers = $headers;
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headers;
            }
        }, $filename);
    }

    /**
     * Get the count of imported records.
     */
    private function getImportedCount($import)
    {
        // This is a simple estimation - in production you might want to track this more precisely
        return DB::table('users')->where('role', 'student')->count();
    }

    /**
     * Show import history/logs.
     */
    public function importHistory()
    {
        $recentStudents = DB::table('users')
            ->where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.students.import-history', compact('recentStudents'));
    }
}
