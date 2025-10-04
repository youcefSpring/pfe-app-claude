@extends('layouts.pfe-app')

@section('page-title', __('app.student_upload'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-upload me-2"></i>{{ __('app.upload') }} {{ __('app.students_from_excel') }}
                </h5>
                <div>
                    <a href="{{ route('admin.students.template') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download me-1"></i>{{ __('app.download_template') }}
                    </a>
                    <a href="{{ route('admin.students.import-history') }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-clock-history me-1"></i>{{ __('app.history') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if(session('validation_errors'))
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>{{ __('app.validation_errors') }}:</h6>
                        <ul class="mb-0">
                            @foreach(session('validation_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Instructions -->
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i>Instructions d'importation</h6>
                    <ul class="mb-0">
                        <li><strong>Structure du fichier:</strong> Les données d'étudiants commencent à la <strong>ligne 5</strong> (ligne 4 = en-têtes)</li>
                        <li><strong>Métadonnées:</strong> Les 3 premières lignes contiennent: Nom du programme, Semestre, Année académique</li>
                        <li><strong>Colonnes requises:</strong> <code>Numero Inscription</code>, <code>Annee Bac</code>, <code>Matricule</code>, <code>Nom</code>, <code>Prenom</code>, <code>Section</code>, <code>Groupe</code></li>
                        <li><strong>Champs obligatoires:</strong> <code>Matricule</code>, <code>Nom</code>, et <code>Prenom</code></li>
                        <li><strong>Email automatique:</strong> <code>matricule@student.university.edu</code></li>
                        <li><strong>Mot de passe par défaut:</strong> <code>student123</code></li>
                        <li><strong>Taille maximale:</strong> 10MB | <strong>Formats:</strong> .xlsx, .xls, .csv</li>
                    </ul>
                </div>

                <form action="{{ route('admin.students.upload.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- File Upload -->
                        <div class="col-md-8 mb-3">
                            <label for="excel_file" class="form-label">{{ __('app.excel_file') }} *</label>
                            <input type="file"
                                   class="form-control @error('excel_file') is-invalid @enderror"
                                   id="excel_file"
                                   name="excel_file"
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Speciality Selection -->
                        <div class="col-md-4 mb-3">
                            <label for="speciality_id" class="form-label">{{ __('app.speciality') }} *</label>
                            <select class="form-select @error('speciality_id') is-invalid @enderror"
                                    id="speciality_id"
                                    name="speciality_id"
                                    required>
                                <option value="">{{ __('app.choose_speciality') }}</option>
                                @foreach($specialities as $speciality)
                                    <option value="{{ $speciality->id }}" {{ old('speciality_id') == $speciality->id ? 'selected' : '' }}>
                                        {{ $speciality->name }} ({{ $speciality->level }})
                                    </option>
                                @endforeach
                            </select>
                            @error('speciality_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-2"></i>{{ __('app.import_students') }}
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary ms-2">
                                <i class="bi bi-arrow-left me-2"></i>{{ __('app.back_to_users') }}
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
                    <i class="bi bi-check-circle text-success me-2"></i>{{ __('app.import_results') }}
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
                                <small>{{ __('app.created') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h3>{{ $details['updated'] }}</h3>
                                <small>{{ __('app.updated') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body">
                                <h3>{{ $details['skipped'] }}</h3>
                                <small>{{ __('app.skipped') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h3>{{ $details['total_processed'] }}</h3>
                                <small>{{ __('app.total') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($details['errors']))
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>{{ __('app.errors') }} ({{ count($details['errors']) }})</h6>
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
                        <h6 class="mb-0">{{ __('app.speciality_information') }}</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">{{ __('app.name') }}:</dt>
                            <dd class="col-sm-9">{{ $details['speciality']['name'] }}</dd>
                            <dt class="col-sm-3">{{ __('app.level') }}:</dt>
                            <dd class="col-sm-9">{{ ucfirst($details['speciality']['level']) }}</dd>
                            <dt class="col-sm-3">{{ __('app.academic_year') }}:</dt>
                            <dd class="col-sm-9">{{ $details['speciality']['academic_year'] }}</dd>
                            @if($details['speciality']['semester'])
                                <dt class="col-sm-3">{{ __('app.semester') }}:</dt>
                                <dd class="col-sm-9">{{ $details['speciality']['semester'] }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
                <a href="{{ route('admin.specialities') }}" class="btn btn-primary">{{ __('app.view_specialities') }}</a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show import results modal if there are results
    @if(session('import_details'))
        const modal = new bootstrap.Modal(document.getElementById('importResultsModal'));
        modal.show();
    @endif
});
</script>
@endpush