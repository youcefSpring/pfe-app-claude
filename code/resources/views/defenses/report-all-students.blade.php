<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procès-Verbal de Soutenance de Mémoire de Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            line-height: 2.3;
            background-color: #f5f5f5;
            color: #000;
        }

        .page-container {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            page-break-after: always;
        }

        .page-container:last-child {
            page-break-after: auto;
        }

        .content-wrapper {
            padding: 1.5cm;
        }

        .republic-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            color: #000;
            margin-bottom: 1px;
            padding-top: 5px;
        }

        .header-section {
            border-bottom: 2px solid #000;
            padding: 8px 1.5cm;
            margin: 0 -1.5cm 8px -1.5cm;
        }

        .header-col {
            font-size: 8px;
            line-height: 1.3;
        }

        .logo-img {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }

        .main-title {
            font-size: 10px;
            font-weight: bold;
            border: 2px solid #000;
            padding: 4px;
            text-align: center;
            margin: 6px 0;
        }

        .content-section {
            font-size: 10px;
            margin: 4px 0;
        }

        .underline-text {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 70px;
            padding: 0 3px 0px;
            color: #000;
        }

        .dynamic-text {
            display: inline-block;
            font-weight: bold;
            min-width: 70px;
            padding: 0 3px 0px;
            color: #000;
        }

        .bold-label {
            font-weight: bold;
        }

        .section-header {
            font-weight: bold;
            font-size: 10px;
            margin: 6px 0 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
        }

        th {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        tbody tr {
            height: 28px;
        }

        .checkbox-box {
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            display: inline-block;
            vertical-align: middle;
            margin-right: 2px;
        }

        .mentions-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 9px;
        }

        .mention-item {
            text-align: center;
            flex: 1;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-weight: bold;
            font-size: 10px;
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

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }

            .page-container {
                box-shadow: none;
                margin: 0;
                width: 100%;
                page-break-after: always;
            }

            .page-container:last-child {
                page-break-after: auto;
            }

            .print-btn,
            .actions-container {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ {{ __('app.print') }}</button>

    @foreach($teamMembers as $member)
        @php
            $userData = $member->user;
        @endphp

        <div class="page-container">
            <!-- Republic Headers -->
            <div class="republic-title">الجمهورية الجزائرية الديمقراطية الشعبية</div>
            <div class="republic-title" style="margin-bottom: 0;">REPUBLIQUE ALGERIENNE DEMOCRATIQUE ET POPULAIRE</div>

            <!-- Header Section with Green Border -->
            <div class="header-section">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <div class="col-span-5 header-col">
                        <div>Ministère de l'Enseignement Supérieur
                    et de la Recherche Scientifique</div>
                        <div>Université M'Hamed BOUGARA - Boumerdès</div>
                        <div>Faculté des Sciences</div>
                        <div>Département : Informatique</div>
                    </div>
                    <div class="col-span-2 text-center">
                        <img src="https://images.cdn-files-a.com/uploads/1598328/800_5bd31cdbbf082.jpg" alt="Logo" class="logo-img mx-auto">
                    </div>
                    <div class="col-span-5 header-col text-right" style="direction: rtl;">
                        <div>وزارة التعليم العالي و البحث العلمي</div>
                        <div>جامعة أحمد بوقرة ـ بومرداس</div>
                        <div>كلية العلوم</div>
                        <div>قسم الاعلام الالي</div>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Main Title -->
                <div class="main-title">Procès-Verbal de Soutenance de Mémoire de Master</div>

                <!-- Defense Info -->
                <div class="content-section">
                    En date du : <span class="dynamic-text">{{ $defense->defense_date ? $defense->defense_date->format('d/m/Y') : '___/___/___' }}</span>
                    a eu lieu la soutenance de Mémoire de Master de l'étudiant(e) :
                </div>

                <div class="content-section">
                    <span class="dynamic-text" style="min-width: 180px;">{{ $userData->name }}</span>
                    né(e) le <span class="dynamic-text">{{ $userData->date_naissance ? $userData->date_naissance->format('d/m/Y') : '___/___/___' }}</span>
                    à <span class="dynamic-text" style="min-width: 120px;">{{ $userData->lieu_naissance ?? '________________' }}</span>
                </div>

                <div class="content-section">
                    Année universitaire : <span class="dynamic-text">{{ $academicYear }}</span>
                </div>

                <div class="content-section">
                    <span class="bold-label">Domaine</span> : Mathématique et Informatique
                </div>

                <div class="content-section">
                    <span class="bold-label">Filière</span> : Informatique
                </div>

                <div class="content-section">
                    <span class="bold-label">Spécialité</span> : <span class="dynamic-text" style="min-width: 200px;">{{ $userData->speciality->name ?? '________________' }}</span>
                </div>

                <div class="content-section">
                    <span class="bold-label">Type de diplôme :</span> Professionnelle.
                </div>

                <!-- Jury Table -->
                <div class="section-header">Devant le Jury composé de :</div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 30px;"></th>
                            <th>Membre du Jury (Nom / Prénom)</th>
                            <th style="width: 80px;">Grade</th>
                            <th style="width: 90px;">Qualité</th>
                            <th style="width: 70px;">Signature</th>
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
                <div class="content-section">
                    <span class="bold-label">Intitulé du sujet :</span> <span class="dynamic-text">{{ $defense->subject->title ?? '' }}</span>
                </div>

                <!-- Results -->
                <div class="section-header">Résultats de la délibération :</div>
                <div class="section-header">Notes attribuées par le Jury :</div>

                <table>
                    <thead>
                        <tr>
                            <th>Manuscrit<br>(6/8)</th>
                            <th>Exposé oral<br>(4/6)</th>
                            <th>Réponses aux<br>questions (5/6)</th>
                            <th>Réalisation<br>(5/-)</th>
                            <th>Note finale<br>du PFE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $defense->manuscript_grade ?? '' }}</td>
                            <td>{{ $defense->oral_grade ?? '' }}</td>
                            <td>{{ $defense->questions_grade ?? '' }}</td>
                            <td>{{ $defense->realization_grade ?? '' }}</td>
                            <td>{{ $defense->final_grade ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Decision -->
                <div class="content-section">
                    Par conséquent, l'étudiant(e) est déclaré(e) :
                    <span class="checkbox-box"></span> <span class="bold-label">Admis(e)</span> -
                    <span class="checkbox-box"></span> <span class="bold-label">Ajourné(e)</span>
                </div>

                <div class="content-section">
                    à l'examen de soutenance de son Mémoire de Master avec la mention :
                </div>

                <!-- Mentions -->
                <div class="mentions-row">
                    <div class="mention-item">
                        <div><span class="checkbox-box"></span> <span class="bold-label">Excellent</span></div>
                        <div>(18≤N≤20)</div>
                    </div>
                    <div class="mention-item">
                        <div><span class="checkbox-box"></span> <span class="bold-label">Très Bien</span></div>
                        <div>(16≤N&lt;18)</div>
                    </div>
                    <div class="mention-item">
                        <div><span class="checkbox-box"></span> <span class="bold-label">Bien</span></div>
                        <div>(14≤N&lt;16)</div>
                    </div>
                    <div class="mention-item">
                        <div><span class="checkbox-box"></span> <span class="bold-label">Assez Bien</span></div>
                        <div>(12≤N&lt;14)</div>
                    </div>
                    <div class="mention-item">
                        <div><span class="checkbox-box"></span> <span class="bold-label">Passable</span></div>
                        <div>(10≤N&lt;12)</div>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="signatures">
                    <div>Le Président du Jury</div>
                    <div>Le Chef de Département</div>
                </div>
            </div>
        </div>
    @endforeach

    <button class="print-btn" onclick="window.print()">🖨️ {{ __('app.print') }}</button>
</body>
</html>
