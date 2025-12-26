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
                ->with('success', __('app.import_successful_message', ['count' => $importedCount, 'name' => $speciality->name]))
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
     * Download a sample Excel template that matches the expected format.
     */
    public function downloadTemplate()
    {
        // Create a new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add metadata rows (rows 1-3)
        $sheet->setCellValue('A1', 'Offre de formation: ingénierie du logiciel et traitement de l\'information');
        $sheet->setCellValue('A2', 'Semestre: M_S3');
        $sheet->setCellValue('A3', 'Année Académique: 2025/2026');

        // Row 4: Warning message
        $sheet->setCellValue('A4', 'Attention : la forme du canevas ne doit pas être modifiée, la colonne section et groupe doit être correcte');
        $sheet->getStyle('A4')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED))->setBold(true);

        // Row 5: Headers (using names that will be correctly processed by WithHeadingRow)
        $headers = ['N°', 'Numero Inscription', 'Annee Bac', 'Matricule', 'Nom', 'Prenom', 'Section', 'Groupe'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }

        // Add sample data starting from row 6
        $sampleData = [
            [1, 'UN35012025202031025123', 2020, '31025107', 'KACED', 'NASSIM TAHA', 'Section_1', 'G_02'],
            [2, 'UN35012025212131066540', 2021, '31066540', 'BENTCHAKAL', 'FATIMA YASMIN', 'Section_1', 'G_02'],
            [3, 'UN35012025212131058079', 2021, '31058079', 'BELHOUT', 'ABDERREZAK', 'Section_1', 'G_02'],
        ];

        $row = 6;
        foreach ($sampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Style the headers
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('A5:H5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFE0E0E0');

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create Excel writer and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'template_etudiants_' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
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
