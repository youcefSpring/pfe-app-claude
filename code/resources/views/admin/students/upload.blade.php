@extends('layouts.pfe-app')

@section('page-title', 'Student Upload')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-upload me-2"></i>Upload Students from Excel
                </h5>
            </div>
            <div class="card-body">
                <!-- Instructions -->
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i>Upload Instructions</h6>
                    <ul class="mb-0">
                        <li>Excel file should contain student data starting from <strong>row 5</strong></li>
                        <li>Headers should be in <strong>row 4</strong></li>
                        <li>Required columns: <code>Numero Inscription</code>, <code>Nom</code>, <code>Prénom</code></li>
                        <li>Optional columns: <code>Matricule</code>, <code>Année Bac</code>, <code>Date de naissance</code>, <code>Section</code>, <code>Groupe</code></li>
                        <li>Available speciality levels: <strong>L2 ING, L3 LMD, L4 ING, L5 ING, M2 LMD</strong></li>
                        <li><strong>Duplicate Prevention:</strong> Students are matched by numero_inscription, matricule, email, or name</li>
                        <li><strong>Department:</strong> All students will be assigned to Computer Science department</li>
                        <li>Maximum file size: 10MB</li>
                        <li>Supported formats: .xlsx, .xls</li>
                    </ul>
                </div>

                <form action="{{ route('admin.students.upload.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- File Upload -->
                        <div class="col-md-6 mb-3">
                            <label for="excel_file" class="form-label">Excel File *</label>
                            <input type="file"
                                   class="form-control @error('excel_file') is-invalid @enderror"
                                   id="excel_file"
                                   name="excel_file"
                                   accept=".xlsx,.xls"
                                   required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Speciality Selection or Creation -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Speciality</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="speciality_option" id="existing_speciality" value="existing" checked>
                                <label class="form-check-label" for="existing_speciality">
                                    Use existing speciality
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="speciality_option" id="new_speciality" value="new">
                                <label class="form-check-label" for="new_speciality">
                                    Create new speciality
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Speciality Selection -->
                    <div id="existing_speciality_section" class="row">
                        <div class="col-12 mb-3">
                            <label for="existing_speciality_id" class="form-label">Select Existing Speciality</label>
                            <select class="form-select" id="existing_speciality_id" name="existing_speciality_id">
                                <option value="">Choose a speciality...</option>
                                @foreach($specialities as $speciality)
                                    <option value="{{ $speciality->id }}">{{ $speciality->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- New Speciality Creation -->
                    <div id="new_speciality_section" class="row" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label for="speciality_name" class="form-label">Speciality Name *</label>
                            <input type="text"
                                   class="form-control @error('speciality_name') is-invalid @enderror"
                                   id="speciality_name"
                                   name="speciality_name"
                                   value="{{ old('speciality_name') }}"
                                   placeholder="e.g., Ingénierie du logiciel et traitement de l'information">
                            @error('speciality_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="speciality_code" class="form-label">Speciality Code</label>
                            <input type="text"
                                   class="form-control @error('speciality_code') is-invalid @enderror"
                                   id="speciality_code"
                                   name="speciality_code"
                                   value="{{ old('speciality_code') }}"
                                   placeholder="e.g., ILTI">
                            @error('speciality_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="speciality_level" class="form-label">Level *</label>
                            <select class="form-select @error('speciality_level') is-invalid @enderror"
                                    id="speciality_level"
                                    name="speciality_level">
                                <option value="">Select level...</option>
                                <option value="L2 ING" {{ old('speciality_level') == 'L2 ING' ? 'selected' : '' }}>L2 ING (2nd Year Engineering)</option>
                                <option value="L3 LMD" {{ old('speciality_level') == 'L3 LMD' ? 'selected' : '' }}>L3 LMD (3rd Year License LMD)</option>
                                <option value="L4 ING" {{ old('speciality_level') == 'L4 ING' ? 'selected' : '' }}>L4 ING (4th Year Engineering)</option>
                                <option value="L5 ING" {{ old('speciality_level') == 'L5 ING' ? 'selected' : '' }}>L5 ING (5th Year Engineering)</option>
                                <option value="M2 LMD" {{ old('speciality_level') == 'M2 LMD' ? 'selected' : '' }}>M2 LMD (2nd Year Master LMD)</option>
                            </select>
                            @error('speciality_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="academic_year" class="form-label">Academic Year *</label>
                            <input type="text"
                                   class="form-control @error('academic_year') is-invalid @enderror"
                                   id="academic_year"
                                   name="academic_year"
                                   value="{{ old('academic_year', date('Y') . '/' . (date('Y') + 1)) }}"
                                   placeholder="e.g., 2024/2025">
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <input type="text"
                                   class="form-control @error('semester') is-invalid @enderror"
                                   id="semester"
                                   name="semester"
                                   value="{{ old('semester') }}"
                                   placeholder="e.g., S1, S2">
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Optional description for the speciality">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-2"></i>Upload Students
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary ms-2">
                                <i class="bi bi-arrow-left me-2"></i>Back to Users
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Import Results Modal -->
@if(session('import_details'))
<div class="modal fade" id="importResultsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle text-success me-2"></i>Import Results
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @php $details = session('import_details'); @endphp

                <div class="row text-center mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h3>{{ $details['created'] }}</h3>
                                <small>Created</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h3>{{ $details['updated'] }}</h3>
                                <small>Updated</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body">
                                <h3>{{ $details['skipped'] }}</h3>
                                <small>Skipped</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h3>{{ $details['total_processed'] }}</h3>
                                <small>Total</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($details['errors']))
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Errors ({{ count($details['errors']) }})</h6>
                        <div class="small">
                            @foreach(array_slice($details['errors'], 0, 10) as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                            @if(count($details['errors']) > 10)
                                <div class="mt-2"><em>... and {{ count($details['errors']) - 10 }} more errors</em></div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Speciality Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Name:</dt>
                            <dd class="col-sm-9">{{ $details['speciality']['name'] }}</dd>
                            <dt class="col-sm-3">Level:</dt>
                            <dd class="col-sm-9">{{ ucfirst($details['speciality']['level']) }}</dd>
                            <dt class="col-sm-3">Academic Year:</dt>
                            <dd class="col-sm-9">{{ $details['speciality']['academic_year'] }}</dd>
                            @if($details['speciality']['semester'])
                                <dt class="col-sm-3">Semester:</dt>
                                <dd class="col-sm-9">{{ $details['speciality']['semester'] }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('admin.specialities') }}" class="btn btn-primary">View Specialities</a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const existingRadio = document.getElementById('existing_speciality');
    const newRadio = document.getElementById('new_speciality');
    const existingSection = document.getElementById('existing_speciality_section');
    const newSection = document.getElementById('new_speciality_section');

    function toggleSections() {
        if (existingRadio.checked) {
            existingSection.style.display = 'block';
            newSection.style.display = 'none';
            // Clear new speciality fields
            newSection.querySelectorAll('input, select, textarea').forEach(field => {
                if (field.name !== 'speciality_option') {
                    field.removeAttribute('required');
                }
            });
            // Make existing speciality required
            document.getElementById('existing_speciality_id').setAttribute('required', 'required');
        } else {
            existingSection.style.display = 'none';
            newSection.style.display = 'block';
            // Make new speciality fields required
            document.getElementById('speciality_name').setAttribute('required', 'required');
            document.getElementById('speciality_level').setAttribute('required', 'required');
            document.getElementById('academic_year').setAttribute('required', 'required');
            // Remove existing speciality requirement
            document.getElementById('existing_speciality_id').removeAttribute('required');
        }
    }

    existingRadio.addEventListener('change', toggleSections);
    newRadio.addEventListener('change', toggleSections);

    // Initialize
    toggleSections();

    // Show import results modal if there are results
    @if(session('import_details'))
        const modal = new bootstrap.Modal(document.getElementById('importResultsModal'));
        modal.show();
    @endif
});
</script>
@endpush