<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procès-Verbal de Soutenance PFE</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .institution-logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }

        .institution-info h1 {
            font-size: 18px;
            margin: 5px 0;
            color: #333;
        }

        .institution-info h2 {
            font-size: 16px;
            margin: 3px 0;
            color: #666;
        }

        .institution-info p {
            font-size: 12px;
            margin: 2px 0;
            color: #777;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 30px 0;
            text-transform: uppercase;
            color: #000;
        }

        .academic-year {
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .section {
            margin: 20px 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px;
            border-left: 4px solid #333;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 6px 10px;
            border: 1px solid #ccc;
            vertical-align: top;
        }

        .info-table td.label {
            background-color: #f8f8f8;
            font-weight: bold;
            width: 25%;
        }

        .jury-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .jury-table th,
        .jury-table td {
            padding: 10px;
            border: 1px solid #333;
            text-align: left;
        }

        .jury-table th {
            background-color: #333;
            color: white;
            font-weight: bold;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .grades-table th,
        .grades-table td {
            padding: 8px;
            border: 1px solid #333;
            text-align: center;
        }

        .grades-table th {
            background-color: #333;
            color: white;
        }

        .final-grade {
            background-color: #f0f8ff;
            font-weight: bold;
            font-size: 14px;
        }

        .mention {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            padding: 10px;
            border: 2px solid #333;
            margin: 20px 0;
        }

        .signatures {
            margin-top: 40px;
        }

        .signature-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            margin-right: 3%;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 11px;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        .student-list {
            list-style: none;
            padding: 0;
        }

        .student-list li {
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }

        .student-list li:last-child {
            border-bottom: none;
        }

        .observations {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #333;
            margin: 20px 0;
            min-height: 80px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="institution-info">
            @if(isset($institution_info['logo_path']))
                <img src="{{ asset($institution_info['logo_path']) }}" alt="Logo" class="institution-logo">
            @endif
            <h1>{{ $institution_info['name'] }}</h1>
            <h2>{{ $institution_info['faculty'] }}</h2>
            <h2>{{ $institution_info['department'] }}</h2>
            <p>{{ $institution_info['address'] }}</p>
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        Procès-Verbal de Soutenance<br>
        Projet de Fin d'Études
    </div>

    <div class="academic-year">
        Année Académique {{ $academic_year }}
    </div>

    <!-- Project Information -->
    <div class="section">
        <div class="section-title">INFORMATIONS DU PROJET</div>
        <table class="info-table">
            <tr>
                <td class="label">Titre du Projet :</td>
                <td colspan="3">{{ $subject->title }}</td>
            </tr>
            <tr>
                <td class="label">Description :</td>
                <td colspan="3">{{ Str::limit($subject->description, 200) }}</td>
            </tr>
            <tr>
                <td class="label">Équipe :</td>
                <td>{{ $team->name }}</td>
                <td class="label">Taille de l'équipe :</td>
                <td>{{ count($student_info) }} étudiants</td>
            </tr>
            <tr>
                <td class="label">Encadrant :</td>
                <td>{{ $project->supervisor->first_name }} {{ $project->supervisor->last_name }}</td>
                <td class="label">Département :</td>
                <td>{{ $project->supervisor->department }}</td>
            </tr>
            @if($project->external_supervisor)
            <tr>
                <td class="label">Encadrant Externe :</td>
                <td>{{ $project->external_supervisor }}</td>
                <td class="label">Entreprise :</td>
                <td>{{ $project->external_company ?? 'N/A' }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Student Information -->
    <div class="section">
        <div class="section-title">ÉTUDIANTS</div>
        <ul class="student-list">
            @foreach($student_info as $student)
            <li>
                <strong>{{ $student['full_name'] }}</strong>
                - ID: {{ $student['student_id'] }}
                - Email: {{ $student['email'] }}
                @if($student['role_in_team'] === 'Team Leader')
                    <em>(Chef d'équipe)</em>
                @endif
            </li>
            @endforeach
        </ul>
    </div>

    <!-- Defense Information -->
    <div class="section">
        <div class="section-title">INFORMATIONS DE LA SOUTENANCE</div>
        <table class="info-table">
            <tr>
                <td class="label">Date :</td>
                <td>{{ $defense->defense_date->format('d/m/Y') }}</td>
                <td class="label">Heure :</td>
                <td>{{ $defense->start_time }} - {{ $defense->end_time }}</td>
            </tr>
            <tr>
                <td class="label">Salle :</td>
                <td>{{ $defense->room->name }}</td>
                <td class="label">Durée :</td>
                <td>{{ $defense->duration }} minutes</td>
            </tr>
        </table>
    </div>

    <!-- Jury Composition -->
    <div class="section">
        <div class="section-title">COMPOSITION DU JURY</div>
        <table class="jury-table">
            <thead>
                <tr>
                    <th>Fonction</th>
                    <th>Nom et Prénom</th>
                    <th>Grade</th>
                    <th>Département</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Président</td>
                    <td>{{ $jury_info['president']['name'] }}</td>
                    <td>{{ $jury_info['president']['title'] }}</td>
                    <td>{{ $jury_info['president']['department'] }}</td>
                    <td style="height: 40px;"></td>
                </tr>
                <tr>
                    <td>Examinateur</td>
                    <td>{{ $jury_info['examiner']['name'] }}</td>
                    <td>{{ $jury_info['examiner']['title'] }}</td>
                    <td>{{ $jury_info['examiner']['department'] }}</td>
                    <td style="height: 40px;"></td>
                </tr>
                <tr>
                    <td>Encadrant</td>
                    <td>{{ $jury_info['supervisor']['name'] }}</td>
                    <td>{{ $jury_info['supervisor']['title'] }}</td>
                    <td>{{ $jury_info['supervisor']['department'] }}</td>
                    <td style="height: 40px;"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Grades -->
    <div class="section">
        <div class="section-title">ÉVALUATION</div>
        <table class="grades-table">
            <thead>
                <tr>
                    <th>Critère d'Évaluation</th>
                    <th>Note (/20)</th>
                    <th>Coefficient</th>
                    <th>Note Pondérée</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Aspect Technique</td>
                    <td>{{ $grades['technical_grade'] ?? 'N/A' }}</td>
                    <td>40%</td>
                    <td>{{ isset($grades['technical_grade']) ? round($grades['technical_grade'] * 0.4, 2) : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Présentation Orale</td>
                    <td>{{ $grades['presentation_grade'] ?? 'N/A' }}</td>
                    <td>30%</td>
                    <td>{{ isset($grades['presentation_grade']) ? round($grades['presentation_grade'] * 0.3, 2) : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Rapport Écrit</td>
                    <td>{{ $grades['report_grade'] ?? 'N/A' }}</td>
                    <td>30%</td>
                    <td>{{ isset($grades['report_grade']) ? round($grades['report_grade'] * 0.3, 2) : 'N/A' }}</td>
                </tr>
                <tr class="final-grade">
                    <td><strong>Note Finale</strong></td>
                    <td><strong>{{ $grades['final_grade'] ?? 'N/A' }}/20</strong></td>
                    <td><strong>100%</strong></td>
                    <td><strong>{{ $grades['final_grade_letter'] ?? 'N/A' }}</strong></td>
                </tr>
            </tbody>
        </table>

        @if($grades['mention'])
        <div class="mention">
            MENTION : {{ strtoupper($grades['mention']) }}
        </div>
        @endif
    </div>

    <!-- Observations -->
    <div class="section">
        <div class="section-title">OBSERVATIONS ET RECOMMANDATIONS</div>
        <div class="observations">
            {{ $grades['comments'] ?? 'Aucune observation particulière.' }}
        </div>
    </div>

    <!-- Decision -->
    <div class="section">
        <div class="section-title">DÉCISION DU JURY</div>
        <table class="info-table">
            <tr>
                <td class="label">Résultat :</td>
                <td>
                    @if(($grades['final_grade'] ?? 0) >= 10)
                        <strong style="color: green;">✓ ADMIS</strong>
                    @else
                        <strong style="color: red;">✗ AJOURNÉ</strong>
                    @endif
                </td>
                <td class="label">Date de délibération :</td>
                <td>{{ $defense->completed_at ? $defense->completed_at->format('d/m/Y H:i') : $defense->defense_date->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-box">
            <strong>Le Président du Jury</strong>
            <div class="signature-line">
                {{ $jury_info['president']['name'] }}
            </div>
        </div>
        <div class="signature-box">
            <strong>L'Examinateur</strong>
            <div class="signature-line">
                {{ $jury_info['examiner']['name'] }}
            </div>
        </div>
        <div class="signature-box">
            <strong>L'Encadrant</strong>
            <div class="signature-line">
                {{ $jury_info['supervisor']['name'] }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Document généré automatiquement le {{ $generated_at->format('d/m/Y à H:i') }} par {{ $generated_by->first_name }} {{ $generated_by->last_name }}</p>
        <p>{{ $institution_info['name'] }} - Système de Gestion PFE</p>
    </div>
</body>
</html>