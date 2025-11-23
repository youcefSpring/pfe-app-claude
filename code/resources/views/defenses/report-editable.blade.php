<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Éditer Procès-Verbal de Soutenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .page-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .student-section {
            border: 2px solid #0d6efd;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            background-color: #f8f9ff;
        }
        .student-header {
            background-color: #0d6efd;
            color: white;
            padding: 10px 15px;
            margin: -20px -20px 20px -20px;
            border-radius: 6px 6px 0 0;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .input-group-text {
            background-color: #e9ecef;
            font-weight: 500;
        }
        .btn-save {
            background-color: #198754;
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .btn-save:hover {
            background-color: #157347;
            color: white;
        }
        .btn-cancel {
            background-color: #6c757d;
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
        }
        .alert-info {
            border-left: 4px solid #0dcaf0;
        }
        .missing-data {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 3px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-file-earmark-text"></i>
                Éditer Procès-Verbal de Soutenance
            </h2>
            <a href="{{ route('defenses.show', $defense) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Information:</strong> Veuillez remplir toutes les données manquantes pour chaque étudiant avant de générer le rapport final.
            Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.
        </div>

        <form action="{{ route('defenses.update-student-data', $defense) }}" method="POST">
            @csrf
            @method('PUT')

            @foreach($teamMembers as $index => $teamMember)
                @php
                    $userData = $teamMember->user;
                    $hasMissingData = !$userData->date_naissance || !$userData->lieu_naissance;
                @endphp

                <div class="student-section">
                    <div class="student-header">
                        <i class="bi bi-person-fill"></i>
                        Étudiant {{ $index + 1 }}: {{ $userData->name }}
                        @if($hasMissingData)
                            <span class="badge bg-warning text-dark ms-2">Données incomplètes</span>
                        @else
                            <span class="badge bg-success ms-2">Données complètes</span>
                        @endif
                    </div>

                    <input type="hidden" name="students[{{ $index }}][user_id]" value="{{ $userData->id }}">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label required-field">Nom complet</label>
                            <input type="text" 
                                   class="form-control @error('students.'.$index.'.name') is-invalid @enderror" 
                                   name="students[{{ $index }}][name]" 
                                   value="{{ old('students.'.$index.'.name', $userData->name) }}"
                                   required>
                            @error('students.'.$index.'.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label required-field">Date de naissance</label>
                            <input type="date" 
                                   class="form-control @error('students.'.$index.'.date_naissance') is-invalid @enderror" 
                                   name="students[{{ $index }}][date_naissance]" 
                                   value="{{ old('students.'.$index.'.date_naissance', $userData->date_naissance?->format('Y-m-d')) }}"
                                   required>
                            @error('students.'.$index.'.date_naissance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(!$userData->date_naissance)
                                <small class="missing-data">
                                    <i class="bi bi-exclamation-triangle"></i> Donnée manquante
                                </small>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required-field">Lieu de naissance</label>
                            <input type="text" 
                                   class="form-control @error('students.'.$index.'.lieu_naissance') is-invalid @enderror" 
                                   name="students[{{ $index }}][lieu_naissance]" 
                                   value="{{ old('students.'.$index.'.lieu_naissance', $userData->lieu_naissance) }}"
                                   placeholder="Ex: Alger, Boumerdès, etc."
                                   required>
                            @error('students.'.$index.'.lieu_naissance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(!$userData->lieu_naissance)
                                <small class="missing-data">
                                    <i class="bi bi-exclamation-triangle"></i> Donnée manquante
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control" 
                                   value="{{ $userData->email }}"
                                   disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Spécialité</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $userData->speciality->name ?? 'Non spécifiée' }}"
                                   disabled>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('defenses.show', $defense) }}" class="btn btn-cancel">
                    <i class="bi bi-x-circle"></i> Annuler
                </a>
                <button type="submit" class="btn btn-save">
                    <i class="bi bi-check-circle"></i> Enregistrer et Générer le Rapport
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>
