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
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.3;
            background-color: #f5f5f5;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page-container {
            width: 210mm;
            height: 297mm;
            background: white;
            position: relative;
            padding: 15mm 20mm;
            overflow: hidden;
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
            margin-bottom: 8px;
        }

        .header-col {
            font-size: 10px;
            line-height: 1.2;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .main-title {
            font-size: 14px;
            font-weight: bold;
            border: 2px solid #000;
            padding: 5px;
            text-align: center;
            margin: 8px 0;
        }

        .content-section {
            font-size: 12px;
            margin: 5px 0;
            line-height: 1.3;
        }

        .dynamic-text {
            display: inline-block;
            font-weight: bold;
            min-width: 70px;
            padding: 0 3px;
            color: #000;
            border-bottom: 1px solid #000;
        }

        .bold-label {
            font-weight: bold;
        }

        .section-header {
            font-weight: bold;
            font-size: 12px;
            margin: 8px 0 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
        }

        th {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        tbody tr {
            height: 24px;
        }

        .checkbox-box {
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            display: inline-block;
            vertical-align: middle;
            margin-right: 3px;
        }

        .mentions-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 10px;
        }

        .mention-item {
            text-align: center;
            flex: 1;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            font-weight: bold;
            font-size: 12px;
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
                height: 100%;
            }

            .print-btn {
                display: none;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ {{ __('app.print') }}</button>

    @foreach($teamMembers as $teamMember)
        @php
            $userData = $teamMember->user;
        @endphp

        <div class="page-container {{ !$loop->last ? 'page-break' : '' }}">
            <!-- Republic Headers -->
            <div class="republic-title">الجمهورية الجزائرية الديمقراطية الشعبية</div>
            <div class="republic-title" style="margin-bottom: 5px;">REPUBLIQUE ALGERIENNE DEMOCRATIQUE ET POPULAIRE</div>

            <!-- Header Section -->
            <div class="header-section">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="header-col" style="flex: 1;">
                        <div>Ministère de l'Enseignement Supérieur</div>
                        <div>et de la Recherche Scientifique</div>
                        <div>Université M'Hamed BOUGARA - Boumerdès</div>
                        <div>Faculté des Sciences</div>
                        <div>Département : Informatique</div>
                    </div>
                    <div style="text-align: center; flex: 0 0 auto; margin: 0 15px;">
                        <img src="https://images.cdn-files-a.com/uploads/1598328/800_5bd31cdbbf082.jpg" alt="Logo" class="logo-img">
                    </div>
                    <div class="header-col" style="flex: 1; text-align: right; direction: rtl;">
                        <div>وزارة التعليم العالي و البحث العلمي</div>
                        <div>جامعة أحمد بوقرة ـ بومرداس</div>
                        <div>كلية العلوم</div>
                        <div>قسم الاعلام الالي</div>
                    </div>
                </div>
            </div>

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
    @endforeach

    <button class="print-btn" onclick="window.print()">🖨️ {{ __('app.print') }}</button>
</body>
</html>

