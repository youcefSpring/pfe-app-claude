<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procès-Verbal de Soutenance de Mémoire de Master</title>
    <style>
        @page {
            size: A4;
            margin: 0.8cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
            line-height: 1.3;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page-container {
            width: 100%;
            height: 275mm; /* Fixed height to fill A4 page (297mm - margins) */
            background: white;
            /* Adjusted padding since we now have page margins */
            padding: 5mm; 
            position: relative;
        }

        .republic-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            color: #000;
            margin-bottom: 2px;
        }

        .header-section {
            border-bottom: 2px solid #000;
            padding: 8px 0;
            margin-bottom: 15px;
        }

        .header-col {
            font-size: 10px;
            line-height: 1.2;
            width: 45%;
            float: left;
        }

        .header-col.center {
            text-align: center;
            width: 10%;
        }

        .header-col.right {
            text-align: right;
            direction: rtl;
            width: 45%;
            font-family: 'DejaVu Sans', sans-serif;
        }

        /* Clearfix for floated columns */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .main-title {
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #000;
            padding: 8px;
            text-align: center;
            margin: 15px 0;
            background-color: #f9f9f9;
        }

        .content-section {
            font-size: 15px;
            margin: 8px 0;
            line-height: 1.5;
        }

        .dynamic-text {
            display: inline-block;
            font-weight: bold;
            min-width: 50px;
            padding: 0 5px;
            color: #000;
            border-bottom: 1px dotted #000;
        }

        .bold-label {
            font-weight: bold;
        }

        .section-header {
            font-weight: bold;
            font-size: 15px;
            margin: 15px 0 8px;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .checkbox-box {
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            display: inline-block;
            vertical-align: middle;
            margin-right: 5px;
        }

        .mentions-row {
            width: 100%;
            margin: 15px 0;
            font-size: 11px;
            overflow: hidden;
        }

        .mention-item {
            float: left;
            width: 20%;
            text-align: center;
        }

        .signatures {
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
            padding: 0 5mm; /* Match container padding */
            font-weight: bold;
            font-size: 15px;
        }

        .signature-box {
            float: left;
            width: 50%;
        }

        .signature-box.right {
            text-align: right;
        }

        .print-btn {
            background-color: #1e3a8a;
            color: white;
            border: none;
            padding: 10px 25px;
            font-size: 13px;
            cursor: pointer;
            border-radius: 5px;
            display: block;
            margin: 15px auto;
        }

        .print-btn:hover {
            background-color: #2563eb;
        }

        .page-break {
            page-break-after: always;
            clear: both;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    @if(!isset($isPdf) || !$isPdf)
        <button class="print-btn" onclick="window.print()">🖨️ {{ __('app.print') }}</button>
    @endif

    @foreach($teamMembers as $teamMember)
        @php
            $userData = $teamMember->user;
        @endphp

        <div class="page-container">
            <!-- Header -->
            <table class="header-table" style="width: 100%; margin-bottom: 15px; border: none;">
                <tr>
                    <td style="width: 45%; text-align: left; vertical-align: top; border: none; padding: 0; font-size: 11px; line-height: 1.3;">
                        <div>République Algérienne Démocratique et Populaire</div>
                        <div>Ministère de l'Enseignement Supérieur et de la Recherche Scientifique</div>
                        <div>Université M'Hamed BOUGARA - Boumerdès</div>
                        <div>Faculté des Sciences</div>
                        <div>Département : Informatique</div>
                    </td>
                    <td style="width: 10%; text-align: center; vertical-align: top; border: none; padding: 0;">
                        <img src="https://images.cdn-files-a.com/uploads/1598328/800_5bd31cdbbf082.jpg" alt="Logo" class="logo-img">
                    </td>
                    <td style="width: 45%; text-align: right; vertical-align: top; border: none; padding: 0; font-size: 11px; line-height: 1.3; direction: rtl; font-family: 'DejaVu Sans', sans-serif;">
                        <div>الجمهورية الجزائرية الديمقراطية الشعبية</div>
                        <div>وزارة التعليم العالي و البحث العلمي</div>
                        <div>جامعة أحمد بوقرة ـ بومرداس</div>
                        <div>كلية العلوم</div>
                        <div>قسم الاعلام الالي</div>
                    </td>
                </tr>
            </table>
            <div style="border-bottom: 2px solid #000; margin-bottom: 15px;"></div>

            <!-- Main Title -->
            <div class="main-title">Procès-Verbal de Soutenance de Mémoire de Master</div>

            <!-- Defense Info -->
            <div class="content-section">
                En date du : <span class="dynamic-text">{{ $defense->defense_date ? $defense->defense_date->format('d/m/Y') : '___/___/___' }}</span>
                a eu lieu la soutenance de Mémoire de Master de l'étudiant(e) :
            </div>

            <div class="content-section">
                <span class="dynamic-text" style="min-width: 200px; font-size: 1.1em;">{{ $userData->name }}</span>
                né(e) le <span class="dynamic-text">{{ $userData->date_naissance ? $userData->date_naissance->format('d/m/Y') : '___/___/___' }}</span>
                à <span class="dynamic-text" style="min-width: 150px;">{{ $userData->lieu_naissance ?? '________________' }}</span>
            </div>

            <div class="content-section">
                Année universitaire : <span class="dynamic-text">{{ $academicYear }}</span>
            </div>

            <div class="content-section">
                <span class="bold-label">Domaine</span> : Mathématique et Informatique
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <span class="bold-label">Filière</span> : Informatique
            </div>

            <div class="content-section">
                <span class="bold-label">Spécialité</span> : <span class="dynamic-text" style="min-width: 250px;">{{ $userData->speciality->name ?? '________________' }}</span>
            </div>

            <div class="content-section">
                <span class="bold-label">Type de diplôme :</span> Professionnelle.
            </div>

            <!-- Jury Table -->
            <div class="section-header">Devant le Jury composé de :</div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;"></th>
                        <th style="width: 45%;">Membre du Jury (Nom / Prénom)</th>
                        <th style="width: 15%;">Grade</th>
                        <th style="width: 20%;">Qualité</th>
                        <th style="width: 15%;">Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $juryRoles = ['Président', 'Examinateur', 'Encadreur'];
                        $juryCount = 0;
                    @endphp

                    @foreach($juries as $jury)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $jury->teacher->name ?? '' }}</td>
                            <td>{{ $jury->teacher->grade ?? '' }}</td>
                            <td><em>{{ ucfirst($jury->role) ?? $juryRoles[$juryCount] ?? '' }}</em></td>
                            <td></td>
                        </tr>
                        @php $juryCount++; @endphp
                    @endforeach

                    @while($juryCount < 3)
                        <tr>
                            <td>{{ $juryCount + 1 }}</td>
                            <td></td>
                            <td></td>
                            <td><em>{{ $juryRoles[$juryCount] ?? '' }}</em></td>
                            <td></td>
                        </tr>
                        @php $juryCount++; @endphp
                    @endwhile
                </tbody>
            </table>

            <!-- Subject Title -->
            <div class="content-section" style="margin-top: 15px;">
                <span class="bold-label">Intitulé du sujet :</span>
                <div style="border: 1px solid #ccc; padding: 10px; margin-top: 5px; background-color: #fcfcfc; min-height: 40px;">
                    {{ $defense->subject->title ?? '' }}
                </div>
            </div>

            <!-- Results -->
            <div class="section-header">Résultats de la délibération :</div>
            <div style="margin-bottom: 5px;">Notes attribuées par le Jury :</div>

            <table>
                <thead>
                    <tr>
                        <th>Manuscrit<br>(6/8)</th>
                        <th>Exposé oral<br>(4/6)</th>
                        <th>Réponses aux<br>questions (5/6)</th>
                        <th>Réalisation<br>(5/-)</th>
                        <th style="background-color: #e0e0e0;">Note finale<br>du PFE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="height: 50px;">
                        <td>{{ $defense->manuscript_grade ?? '' }}</td>
                        <td>{{ $defense->oral_grade ?? '' }}</td>
                        <td>{{ $defense->questions_grade ?? '' }}</td>
                        <td>{{ $defense->realization_grade ?? '' }}</td>
                        <td style="font-weight: bold; font-size: 1.2em;">{{ $defense->final_grade ?? '' }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Decision -->
            <div class="content-section" style="margin-top: 20px;">
                Par conséquent, l'étudiant(e) est déclaré(e) :
                <span style="margin-left: 20px;">
                    <span class="checkbox-box"></span> <span class="bold-label">Admis(e)</span>
                </span>
                <span style="margin-left: 20px;">
                    <span class="checkbox-box"></span> <span class="bold-label">Ajourné(e)</span>
                </span>
            </div>

            <div class="content-section">
                à l'examen de soutenance de son Mémoire de Master avec la mention : ____________________
            </div>

            <!-- Mentions -->
            <div class="mentions-row clearfix">
                <div class="mention-item">
                    <div><span class="checkbox-box"></span> <span class="bold-label">Excellent</span></div>
                    <div style="font-size: 9px;">(18≤N≤20)</div>
                </div>
                <div class="mention-item">
                    <div><span class="checkbox-box"></span> <span class="bold-label">Très Bien</span></div>
                    <div style="font-size: 9px;">(16≤N&lt;18)</div>
                </div>
                <div class="mention-item">
                    <div><span class="checkbox-box"></span> <span class="bold-label">Bien</span></div>
                    <div style="font-size: 9px;">(14≤N&lt;16)</div>
                </div>
                <div class="mention-item">
                    <div><span class="checkbox-box"></span> <span class="bold-label">Assez Bien</span></div>
                    <div style="font-size: 9px;">(12≤N&lt;14)</div>
                </div>
                <div class="mention-item">
                    <div><span class="checkbox-box"></span> <span class="bold-label">Passable</span></div>
                    <div style="font-size: 9px;">(10≤N&lt;12)</div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="signatures clearfix">
                <div class="signature-box">Le Président du Jury</div>
                <div class="signature-box right">Le Chef de Département</div>
            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>


