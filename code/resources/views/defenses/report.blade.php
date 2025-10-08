<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procès-Verbal de Soutenance de Mémoire de Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0.5in;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
            }
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.2;
        }
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }
        .header-section {
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .logo {
            height: 80px;
        }
        .header-text {
            text-align: right;
        }
        .university-name {
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 5px;
        }
        .faculty-name {
            font-size: 12pt;
            margin-bottom: 5px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        .form-field {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            margin: 0 5px;
        }
        .table-custom {
            border: 1px solid #000;
            margin-bottom: 20px;
        }
        .table-custom th, .table-custom td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }
        .table-custom th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .results-section {
            margin-bottom: 20px;
        }
        .checkbox-group {
            margin: 10px 0;
        }
        .checkbox-item {
            margin-right: 20px;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            width: 200px;
            display: inline-block;
        }
        .mention-table {
            width: 100%;
            margin-top: 10px;
        }
        .mention-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .underline {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    @php
        $universityInfo = \App\Models\Setting::getUniversityInfo();
        $logo = \App\Models\Setting::getUniversityLogo();

        // Get student data
        $student = null;
        if ($defense->project && $defense->project->team && $defense->project->team->members) {
            $student = $defense->project->team->members->first()?->user;
        }

        // Get president of jury
        $president = $defense->juries->where('role', 'president')->first()?->teacher;
        $examiner = $defense->juries->where('role', 'examiner')->first()?->teacher;
        $supervisor = $defense->juries->where('role', 'supervisor')->first()?->teacher;

        // Default values for missing data
        $studentName = $student ? $student->first_name . ' ' . $student->last_name : 'NOM Prénom';
        $birthDate = $student && $student->date_naissance ? \Carbon\Carbon::parse($student->date_naissance)->format('d/m/Y') : '__/__/____';
        $birthPlace = $student ? $student->lieu_naissance ?? '___________' : '___________';
        $academicYear = $defense->project?->academic_year ?? '2024/2025';
        $speciality = $defense->project?->team?->speciality?->name ?? 'Informatique';
        $subjectTitle = $defense->subject?->title ?? '';
    @endphp

    <div class="container">
        <div class="header-section">
            <div class="row align-items-center">
                <div class="col-6">
                    @if($logo)
                        <img src="{{ Storage::url($logo) }}" alt="Logo" class="logo">
                    @else
                        <img src="https://ubins.univ-boumerdes.dz/assets/images/LOGO.png" alt="Logo" class="logo">
                    @endif
                </div>
                <div class="col-6 header-text">
                    <div class="university-name">{{ $universityInfo['name_fr'] ?? 'Université de Boumerdes' }}</div>
                    <div class="faculty-name">{{ $universityInfo['faculty_fr'] ?? 'Faculté des Sciences' }}</div>
                    <div class="faculty-name">{{ $universityInfo['department_fr'] ?? 'Département d\'Informatique' }}</div>
                </div>
            </div>
        </div>

        <h3 class="title">Procès-Verbal de Soutenance de Mémoire de Master</h3>

        <p>En date du : <span class="form-field">{{ $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('d/m/Y') : '__/__/____' }}</span> a eu lieu la soutenance de Mémoire de
        Master de l'étudiant(e) : <span class="form-field">{{ $studentName }}</span> né(e) le
        <span class="form-field">{{ $birthDate }}</span> à <span class="form-field">{{ $birthPlace }}</span></p>

        <p>Année universitaire : <span class="form-field">{{ $academicYear }}</span></p>

        <p><strong>Domaine</strong> : Mathématique et Informatique</p>

        <p><strong>Filière</strong> : Informatique</p>

        <p><strong>Spécialité</strong> : <span class="form-field">{{ $speciality }}</span></p>

        <p><strong>Type de diplôme</strong> : Professionnelle.</p>

        <p>Devant le Jury composé de :</p>

        <table class="table table-bordered table-custom">
            <thead>
                <tr>
                    <th>Membre du Jury (Nom / Prénom)</th>
                    <th>Grade</th>
                    <th>Qualité</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $president ? $president->name : '_________________' }}</td>
                    <td>{{ $president ? $president->grade ?? '________' : '________' }}</td>
                    <td><strong>Président</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ $examiner ? $examiner->name : '_________________' }}</td>
                    <td>{{ $examiner ? $examiner->grade ?? '________' : '________' }}</td>
                    <td><strong>Examinateur</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ $supervisor ? $supervisor->name : '_________________' }}</td>
                    <td>{{ $supervisor ? $supervisor->grade ?? '________' : '________' }}</td>
                    <td><strong>Encadreur</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <p><strong>Intitulé du sujet</strong> :<br>
        «<span class="form-field" style="min-width: 500px;">{{ $subjectTitle }}</span><br>
        <span class="form-field" style="min-width: 500px;"></span>»</p>

        <div class="results-section">
            <h5 class="underline">Résultats de la délibération :</h5>

            <p><strong>Notes attribuées par le Jury :</strong></p>

            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th>Manuscrit (6/8)</th>
                        <th>Exposé oral (4/6)</th>
                        <th>Réponses aux questions (5/6)</th>
                        <th>Réalisation (5/-)</th>
                        <th>Note finale du PFE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $defense->report?->manuscript_score ?? '' }}</td>
                        <td>{{ $defense->report?->presentation_score ?? '' }}</td>
                        <td>{{ $defense->report?->questions_score ?? '' }}</td>
                        <td>{{ $defense->report?->realization_score ?? '' }}</td>
                        <td>{{ $defense->final_grade ?? '' }}</td>
                    </tr>
                </tbody>
            </table>

            <p>Par conséquent, l'étudiant(e) est déclaré(e) :</p>

            <div class="checkbox-group">
                <span class="checkbox-item">
                    @if($defense->final_grade && $defense->final_grade >= 10)
                        ☑ <strong>Admis(e)</strong>
                    @else
                        □ <strong>Admis(e)</strong>
                    @endif
                </span>
                <span class="checkbox-item">
                    @if($defense->final_grade && $defense->final_grade < 10)
                        ☑ <strong>Ajourné(e)</strong>
                    @else
                        □ <strong>Ajourné(e)</strong>
                    @endif
                </span>
            </div>

            <p>à l'examen de soutenance de son Mémoire de Master avec la mention :</p>

            <table class="table table-bordered mention-table">
                <tr>
                    <td>
                        @if($defense->final_grade && $defense->final_grade >= 10 && $defense->final_grade < 12)
                            ☑ <strong>Passable</strong>
                        @else
                            □ <strong>Passable</strong>
                        @endif
                    </td>
                    <td>
                        @if($defense->final_grade && $defense->final_grade >= 12 && $defense->final_grade < 14)
                            ☑ <strong>Assez Bien</strong>
                        @else
                            □ <strong>Assez Bien</strong>
                        @endif
                    </td>
                    <td>
                        @if($defense->final_grade && $defense->final_grade >= 14 && $defense->final_grade < 16)
                            ☑ <strong>Bien</strong>
                        @else
                            □ <strong>Bien</strong>
                        @endif
                    </td>
                    <td>
                        @if($defense->final_grade && $defense->final_grade >= 16 && $defense->final_grade < 18)
                            ☑ <strong>Très Bien</strong>
                        @else
                            □ <strong>Très Bien</strong>
                        @endif
                    </td>
                    <td>
                        @if($defense->final_grade && $defense->final_grade >= 18)
                            ☑ <strong>Excellent</strong>
                        @else
                            □ <strong>Excellent</strong>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>(10≤N<12)</td>
                    <td>(12≤N<14)</td>
                    <td>(14≤N<16)</td>
                    <td>(16≤N<18)</td>
                    <td>(18≤N≤20)</td>
                </tr>
            </table>
        </div>

        <div class="signature-section row">
            <div class="col-6 text-center">
                <p>Le Président du Jury</p>
                <div class="signature-line"></div>
            </div>
            <div class="col-6 text-center">
                <p>Le Chef de Département</p>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>