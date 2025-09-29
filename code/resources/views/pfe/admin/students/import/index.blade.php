@extends('layouts.admin')

@section('title', __('Import Students'))
@section('page-title', __('Import Students'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / {{ __('Import Students') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-0">{{ __('Import Students') }}</h1>
        <p class="text-muted">{{ __('Bulk import student data from Excel or CSV files') }}</p>
    </div>

    <div class="row g-4">
        <!-- Import Process -->
        <div class="col-lg-8">
            <!-- Step 1: File Upload -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <span class="badge bg-primary me-2">1</span>
                        {{ __('Upload File') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form id="import-form" method="POST" action="{{ route('pfe.admin.students.import.import') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="import_file" class="form-label">{{ __('Select File') }}</label>
                            <input type="file" class="form-control @error('import_file') is-invalid @enderror"
                                   id="import_file" name="import_file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">
                                {{ __('Supported formats: Excel (.xlsx, .xls) and CSV (.csv). Maximum file size: 10MB') }}
                            </div>
                            @error('import_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="academic_year" class="form-label">{{ __('Academic Year') }}</label>
                            <select class="form-select @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" required>
                                <option value="">{{ __('Select academic year') }}</option>
                                <option value="2024-2025" {{ old('academic_year', '2024-2025') == '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                                <option value="2023-2024" {{ old('academic_year') == '2023-2024' ? 'selected' : '' }}>2023-2024</option>
                                <option value="2025-2026" {{ old('academic_year') == '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                            </select>
                            @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="default_level" class="form-label">{{ __('Default Level') }}</label>
                                <select class="form-select @error('default_level') is-invalid @enderror" id="default_level" name="default_level">
                                    <option value="">{{ __('Use file data') }}</option>
                                    <option value="L3" {{ old('default_level') == 'L3' ? 'selected' : '' }}>{{ __('License 3') }}</option>
                                    <option value="M1" {{ old('default_level') == 'M1' ? 'selected' : '' }}>{{ __('Master 1') }}</option>
                                    <option value="M2" {{ old('default_level') == 'M2' ? 'selected' : '' }}>{{ __('Master 2') }}</option>
                                </select>
                                @error('default_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="default_specialty" class="form-label">{{ __('Default Specialty') }}</label>
                                <input type="text" class="form-control @error('default_specialty') is-invalid @enderror"
                                       id="default_specialty" name="default_specialty" value="{{ old('default_specialty') }}"
                                       placeholder="{{ __('e.g., Computer Science') }}">
                                @error('default_specialty')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing" value="1"
                                       {{ old('update_existing') ? 'checked' : '' }}>
                                <label class="form-check-label" for="update_existing">
                                    {{ __('Update existing students (match by email or student ID)') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="send_welcome_emails" name="send_welcome_emails" value="1"
                                       {{ old('send_welcome_emails', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_welcome_emails">
                                    {{ __('Send welcome emails to new students') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="previewImport()">
                                <i class="fas fa-eye me-2"></i>{{ __('Preview') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>{{ __('Import Students') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Import Results (if any) -->
            @if(session('import_results'))
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <span class="badge bg-success me-2">✓</span>
                        {{ __('Import Results') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ session('import_results')['success'] ?? 0 }}</h3>
                                <small class="text-muted">{{ __('Successfully imported') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">{{ session('import_results')['updated'] ?? 0 }}</h3>
                                <small class="text-muted">{{ __('Updated existing') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-danger">{{ session('import_results')['errors'] ?? 0 }}</h3>
                                <small class="text-muted">{{ __('Errors') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-info">{{ session('import_results')['total'] ?? 0 }}</h3>
                                <small class="text-muted">{{ __('Total processed') }}</small>
                            </div>
                        </div>
                    </div>

                    @if(session('import_results')['errors'] > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('Some records could not be imported. Download the error report for details.') }}
                        <a href="#" class="btn btn-sm btn-outline-warning ms-2">
                            <i class="fas fa-download me-1"></i>{{ __('Download Error Report') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Instructions & Template -->
        <div class="col-lg-4">
            <!-- Template Download -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ __('Download Template') }}</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">{{ __('Download the Excel template to ensure your data is formatted correctly.') }}</p>
                    <div class="d-grid">
                        <a href="{{ route('pfe.admin.students.import.template') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>{{ __('Download Template') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Required Fields -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ __('Required Fields') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>{{ __('first_name') }}</strong> - {{ __('Student first name') }}
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>{{ __('last_name') }}</strong> - {{ __('Student last name') }}
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>{{ __('email') }}</strong> - {{ __('Valid email address') }}
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>{{ __('student_id') }}</strong> - {{ __('Unique student ID') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Optional Fields -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ __('Optional Fields') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1">
                            <i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i>
                            <strong>{{ __('phone') }}</strong> - {{ __('Phone number') }}
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i>
                            <strong>{{ __('level') }}</strong> - {{ __('L3, M1, M2') }}
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i>
                            <strong>{{ __('specialty') }}</strong> - {{ __('Field of study') }}
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i>
                            <strong>{{ __('date_of_birth') }}</strong> - {{ __('YYYY-MM-DD format') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Import History -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ __('Recent Imports') }}</h6>
                </div>
                <div class="card-body">
                    @forelse($recentImports ?? [] as $import)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <small class="fw-medium">{{ $import->filename ?? 'students_2024.xlsx' }}</small>
                            <br>
                            <small class="text-muted">{{ $import->created_at ? $import->created_at->diffForHumans() : '2 hours ago' }}</small>
                        </div>
                        <div>
                            <span class="badge bg-{{ ($import->status ?? 'success') == 'success' ? 'success' : 'warning' }}">
                                {{ __(ucfirst($import->status ?? 'success')) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="small text-muted mb-0">{{ __('No recent imports') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImport() {
    const formData = new FormData(document.getElementById('import-form'));

    if (!formData.get('import_file')) {
        alert('{{ __("Please select a file first") }}');
        return;
    }

    fetch('{{ route("pfe.admin.students.import.preview") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPreviewModal(data.preview);
        } else {
            alert(data.message || '{{ __("Error previewing file") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("Error previewing file") }}');
    });
}

function showPreviewModal(preview) {
    // Create and show preview modal
    const modalHtml = `
        <div class="modal fade" id="previewModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Import Preview') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>{{ __('Records to import:') }}</strong> ${preview.total}</p>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        ${preview.headers.map(h => `<th>${h}</th>`).join('')}
                                    </tr>
                                </thead>
                                <tbody>
                                    ${preview.rows.map(row =>
                                        `<tr>${row.map(cell => `<td>${cell || ''}</td>`).join('')}</tr>`
                                    ).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="button" class="btn btn-primary" onclick="proceedWithImport()">{{ __('Proceed with Import') }}</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();

    // Clean up modal after it's closed
    document.getElementById('previewModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function proceedWithImport() {
    document.getElementById('import-form').submit();
}

// File validation
document.getElementById('import_file').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            alert('{{ __("File size must be less than 10MB") }}');
            this.value = '';
            return;
        }

        const allowedTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'application/vnd.ms-excel', // .xls
            'text/csv' // .csv
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('{{ __("Please select a valid Excel or CSV file") }}');
            this.value = '';
            return;
        }
    }
});
</script>
@endpush