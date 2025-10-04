<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procès-Verbal de Soutenance de Mémoire de Master</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }

        .header-left {
            flex: 1;
            text-align: left;
        }

        .header-center {
            flex: 0 0 120px;
            text-align: center;
            margin: 0 20px;
        }

        .header-right {
            flex: 1;
            text-align: right;
            direction: rtl;
        }

        .logo {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            border: 2px solid #000;
            padding: 8px;
            margin: 20px 0;
        }

        .field-row {
            margin: 8px 0;
            line-height: 1.6;
        }

        .field-row strong {
            font-weight: bold;
        }

        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-bottom: 2px;
        }

        .jury-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .jury-table th,
        .jury-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .jury-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .results-table th,
        .results-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .results-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .mention-section {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .mention-box {
            border: 1px solid #000;
            padding: 8px;
            margin: 5px;
            text-align: center;
            min-width: 120px;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-block {
            text-align: center;
            width: 200px;
        }

        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 300px;
            padding-bottom: 2px;
        }

        .arabic {
            direction: rtl;
            text-align: right;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    @php
        $universityInfo = \App\Models\Setting::getUniversityInfo();
        $logo = \App\Models\Setting::getUniversityLogo();
    @endphp

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div><strong>{{ $universityInfo['republic_fr'] }}</strong></div>
            <div><strong>{{ $universityInfo['ministry_fr'] }}</strong></div>
            <div><strong>{{ $universityInfo['university_name_fr'] }}</strong></div>
            <div><strong>{{ $universityInfo['faculty_fr'] }}</strong></div>
            <div><strong>{{ $universityInfo['department_fr'] }}</strong></div>
        </div>

        <div class="header-center">
            @if($logo)
                <img src="{{ public_path(str_replace('/storage', 'storage/app/public', $logo)) }}" alt="University Logo" class="logo">
            @endif
        </div>

        <div class="header-right arabic">
            <div><strong>{{ $universityInfo['republic_ar'] }}</strong></div>
            <div><strong>{{ $universityInfo['ministry_ar'] }}</strong></div>
            <div><strong>{{ $universityInfo['university_name_ar'] }}</strong></div>
            <div><strong>{{ $universityInfo['faculty_ar'] }}</strong></div>
            <div><strong>{{ $universityInfo['department_ar'] }}</strong></div>
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        Procès-Verbal de Soutenance de Mémoire de Master
    </div>

    <!-- Defense Information -->
    <div class="field-row">
        En date du <span class="underline">{{ $defense->defense_date ? $defense->defense_date->format('d/m/Y') : '.......................' }}</span>
        à eu lieu la soutenance de Mémoire de Master de l'étudiant(e) :
    </div>

    <div class="field-row">
        <strong class="underline">
            {{ $defense->project->team->members->map(fn($member) => $member->user->name)->join(' - ') }}
        </strong>
        né(e) le <span class="underline">................</span> à <span class="underline">................</span>
    </div>

    <div class="field-row">
        Année universitaire : <span class="underline">{{ $defense->project->academic_year ?? '2025/2026' }}</span>
    </div>

    <div class="field-row">
        <strong>Domaine</strong> : Mathématique et Informatique
    </div>

    <div class="field-row">
        <strong>Filière</strong> : Informatique
    </div>

    <div class="field-row">
        <strong>Spécialité</strong> : <span class="underline">{{ $defense->project->team->speciality->name ?? '......................................................' }}</span>
    </div>

    <div class="field-row">
        <strong>Type de diplôme</strong> : Professionnelle.
    </div>

    <!-- Jury Composition -->
    <div class="field-row" style="margin-top: 30px;">
        Devant le Jury composé de :
    </div>

    <table class="jury-table">
        <thead>
            <tr>
                <th style="width: 50px;"></th>
                <th>Membre du Jury (Nom / Prénom)</th>
                <th>Grade</th>
                <th>Qualité</th>
                <th>Signature</th>
            </tr>
        </thead>
        <tbody>
            @foreach($defense->juries->take(3) as $index => $jury)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $jury->teacher->name }}</td>
                    <td>{{ $jury->teacher->grade ?? '' }}</td>
                    <td>
                        @if($jury->role === 'president')
                            <em>Président</em>
                        @elseif($jury->role === 'examiner')
                            <em>Examinateur</em>
                        @elseif($jury->role === 'supervisor')
                            <em>Encadreur</em>
                        @endif
                    </td>
                    <td>&nbsp;</td>
                </tr>
            @endforeach

            @for($i = $defense->juries->count(); $i < 3; $i++)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>
                        @if($i === 0)
                            <em>Président</em>
                        @elseif($i === 1)
                            <em>Examinateur</em>
                        @else
                            <em>Encadreur</em>
                        @endif
                    </td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- Subject Title -->
    <div class="field-row" style="margin-top: 30px;">
        <strong>Intitulé du sujet :</strong>
    </div>
    <div style="margin: 10px 0;">
        «<span class="dotted-line">{{ $defense->project->subject->title ?? '' }}</span>
    </div>
    <div style="margin: 10px 0;">
        <span class="dotted-line" style="width: 100%;"></span> ».
    </div>

    <!-- Results -->
    <div style="margin-top: 30px;">
        <strong>Résultats de la délibération :</strong>
    </div>

    <div style="margin: 10px 0;">
        <strong>Notes attribuées par le Jury :</strong>
    </div>

    <table class="results-table">
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
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>

    <div class="field-row" style="margin-top: 20px;">
        Par conséquent, l'étudiant(e) est déclaré(e) :
    </div>

    <div style="margin: 10px 0;">
        <span class="checkbox"></span> <strong>Admis(e)</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span class="checkbox"></span> <strong>Ajourné(e)</strong>
    </div>

    <div class="field-row">
        à l'examen de soutenance de son Mémoire de Master avec la mention :
    </div>

    <!-- Mentions -->
    <div class="mention-section">
        <div class="mention-box">
            <span class="checkbox"></span><strong>Excellent</strong><br>
            (18≤N≤20)
        </div>
        <div class="mention-box">
            <span class="checkbox"></span><strong>Très Bien</strong><br>
            (16≤N<18)
        </div>
        <div class="mention-box">
            <span class="checkbox"></span><strong>Bien</strong><br>
            (14≤N<16)
        </div>
        <div class="mention-box">
            <span class="checkbox"></span><strong>Assez Bien</strong><br>
            (12≤N<14)
        </div>
        <div class="mention-box">
            <span class="checkbox"></span><strong>Passable</strong><br>
            (10≤N<12)
        </div>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-block">
            <strong>Le Président du Jury</strong>
            <div style="height: 60px;"></div>
            <div style="border-top: 1px solid #000; margin-top: 20px;"></div>
        </div>
        <div class="signature-block">
            <strong>Le Chef de Département</strong>
            <div style="height: 60px;"></div>
            <div style="border-top: 1px solid #000; margin-top: 20px;"></div>
        </div>
    </div>
</body>
</html>