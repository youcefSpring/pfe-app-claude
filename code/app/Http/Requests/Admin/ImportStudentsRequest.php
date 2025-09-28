<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('admin_pfe') ||
            auth()->user()->hasRole('chef_master')
        );
    }

    public function rules(): array
    {
        return [
            'excel_file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240', // 10MB
                function ($attribute, $value, $fail) {
                    // Validate file is not corrupted
                    try {
                        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($value->getPathname());
                        $reader->setReadDataOnly(true);
                        $spreadsheet = $reader->load($value->getPathname());

                        // Check if file has at least one worksheet
                        if ($spreadsheet->getSheetCount() === 0) {
                            $fail('The Excel file must contain at least one worksheet.');
                        }

                        // Check if the active sheet has data
                        $sheet = $spreadsheet->getActiveSheet();
                        $highestRow = $sheet->getHighestRow();

                        if ($highestRow < 2) {
                            $fail('The Excel file must contain at least one data row besides the header.');
                        }

                        // Validate maximum number of rows
                        if ($highestRow > 1000) {
                            $fail('The Excel file cannot contain more than 1000 rows.');
                        }

                    } catch (\Exception $e) {
                        $fail('The Excel file is corrupted or cannot be read: ' . $e->getMessage());
                    }
                }
            ],
            'update_existing' => 'boolean',
            'send_notifications' => 'boolean',
            'default_password_type' => 'nullable|in:generated,email_based,custom',
            'custom_password' => [
                'nullable',
                'required_if:default_password_type,custom',
                'string',
                'min:8',
                'max:50',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
            'force_password_reset' => 'boolean',
            'validate_only' => 'boolean',
            'dry_run' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'excel_file.required' => 'Please select an Excel file to upload.',
            'excel_file.file' => 'The uploaded file is not valid.',
            'excel_file.mimes' => 'Only Excel files (.xlsx, .xls) are allowed.',
            'excel_file.max' => 'The Excel file size cannot exceed 10MB.',
            'update_existing.boolean' => 'Update existing option must be true or false.',
            'send_notifications.boolean' => 'Send notifications option must be true or false.',
            'default_password_type.in' => 'Invalid password type selected.',
            'custom_password.required_if' => 'Custom password is required when custom password type is selected.',
            'custom_password.min' => 'Password must be at least 8 characters long.',
            'custom_password.max' => 'Password cannot exceed 50 characters.',
            'custom_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'force_password_reset.boolean' => 'Force password reset option must be true or false.'
        ];
    }

    public function attributes(): array
    {
        return [
            'excel_file' => 'Excel file',
            'update_existing' => 'update existing students',
            'send_notifications' => 'send notifications',
            'default_password_type' => 'password type',
            'custom_password' => 'custom password',
            'force_password_reset' => 'force password reset'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default values for boolean fields
        $this->merge([
            'update_existing' => $this->boolean('update_existing', false),
            'send_notifications' => $this->boolean('send_notifications', true),
            'force_password_reset' => $this->boolean('force_password_reset', true),
            'validate_only' => $this->boolean('validate_only', false),
            'dry_run' => $this->boolean('dry_run', false)
        ]);

        // Set default password type if not provided
        if (!$this->has('default_password_type')) {
            $this->merge(['default_password_type' => 'generated']);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation after basic rules
            if ($this->hasFile('excel_file') && $this->file('excel_file')->isValid()) {
                try {
                    // Validate Excel structure
                    $this->validateExcelStructure($validator);
                } catch (\Exception $e) {
                    $validator->errors()->add('excel_file', 'Excel validation failed: ' . $e->getMessage());
                }
            }
        });
    }

    private function validateExcelStructure($validator): void
    {
        $file = $this->file('excel_file');

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            // Get headers
            $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
            $normalizedHeaders = array_map(function($header) {
                return strtolower(str_replace([' ', '-'], '_', trim($header)));
            }, $headers);

            // Required columns
            $requiredColumns = ['first_name', 'last_name', 'email', 'student_id', 'academic_year', 'specialization'];
            $missingColumns = array_diff($requiredColumns, $normalizedHeaders);

            if (!empty($missingColumns)) {
                $validator->errors()->add(
                    'excel_file',
                    'Missing required columns: ' . implode(', ', $missingColumns)
                );
            }

            // Check for duplicate headers
            $duplicateHeaders = array_diff_assoc($normalizedHeaders, array_unique($normalizedHeaders));
            if (!empty($duplicateHeaders)) {
                $validator->errors()->add(
                    'excel_file',
                    'Duplicate column headers found: ' . implode(', ', $duplicateHeaders)
                );
            }

            // Sample data validation (check first few rows)
            $sampleRows = min(5, $sheet->getHighestRow() - 1); // Check first 5 data rows
            for ($row = 2; $row <= $sampleRows + 1; $row++) {
                $rowData = $sheet->rangeToArray("A{$row}:" . $sheet->getHighestColumn() . $row)[0];

                // Check if required fields are not empty in sample rows
                foreach ($requiredColumns as $index => $column) {
                    $columnIndex = array_search($column, $normalizedHeaders);
                    if ($columnIndex !== false && empty(trim($rowData[$columnIndex] ?? ''))) {
                        // Only warn for sample validation, don't fail completely
                        break;
                    }
                }

                // Check email format in sample
                $emailIndex = array_search('email', $normalizedHeaders);
                if ($emailIndex !== false && !empty($rowData[$emailIndex])) {
                    if (!filter_var(trim($rowData[$emailIndex]), FILTER_VALIDATE_EMAIL)) {
                        $validator->errors()->add(
                            'excel_file',
                            "Invalid email format found in row {$row}: " . $rowData[$emailIndex]
                        );
                    }
                }
            }

        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            $validator->errors()->add('excel_file', 'Cannot read Excel file: ' . $e->getMessage());
        } catch (\Exception $e) {
            $validator->errors()->add('excel_file', 'Excel file validation error: ' . $e->getMessage());
        }
    }
}