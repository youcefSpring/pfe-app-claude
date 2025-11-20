<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procès-Verbal de Soutenance de Mémoire de Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .a4-container {
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            padding: 1.5cm;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .arabic {
            direction: rtl;
            text-align: right;
            font-family: 'DejaVu Sans', sans-serif;
        }
        .french {
            text-align: left;
        }
        .title {
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            font-size: 1.1em;
        }
        .table-bordered-custom th, .table-bordered-custom td {
            border: 0.7px solid #000 !important;
            padding: 4px;
            vertical-align: top;
        }
        .form-control {
            border: none;
            border-bottom: 1px solid #000;
            border-radius: 0;
            padding-left: 0;
            padding-right: 0;
            background-color: transparent;
            font-size: 0.9em;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-right: 10px;
        }
        .checkbox-container input {
            margin-right: 5px;
        }
        .signature-section {
            margin-top: 30px;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .logo {
            max-height: 60px;
        }
        .header-text {
            font-size: 0.75em;
            line-height: 1.2;
        }
        .small-text {
            font-size: 0.9em;
        }
        .table-header {
            font-weight: bold;
        }
        @media print {
            body {
                background-color: white;
            }
            .a4-container {
                box-shadow: none;
                margin: 0;
                padding: 1.5cm;
            }
        }
    </style>
</head>
<body>
    <div class="a4-container">
        <!-- Compact Header Section with Logo in Middle -->
        <div class="header row align-items-center">
            <div class="col-4 arabic header-text">
                <p class="mb-1">وزارة التعليم العالي و البحث العلمي</p>
                <p class="mb-1">جامعة أمحمد بوقرة - بومرداس</p>
                <p class="mb-0">كلية العلوم</p>
                <p class="mb-0">قسم الاعلام الالي</p>
            </div>
            <div class="col-4 logo-container">
                <img src="{{ $logo_path ?? 'https://upload.wikimedia.org/wikipedia/ar/a/a7/Logo-umbb-crsic.png' }}" alt="University Logo" class="logo">
            </div>
            <div class="col-4 french header-text">
                <p class="mb-1">Ministère de l'Enseignement Supérieur et de la Recherche Scientifique</p>
                <p class="mb-1">Université M'Hamed BOUGARA</p>
                <p class="mb-1">Boumerdès</p>
                <p class="mb-0">Faculté des Sciences</p>
                <p class="mb-0">Département : Informatique</p>
            </div>
        </div>

        <!-- Title -->
        <div class="title">
            <strong>Procès-Verbal de Soutenance de Mémoire de Master</strong>
        </div>

        <!-- Student Information -->
        <div class="mb-3 small-text">
            <p>En date du : <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 150px;">{{ $defense['date'] ?? '' }}</span> a eu lieu la soutenance de Mémoire de Master de l'étudiant(e) : <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 200px;">{{ $team['members'][0]['name'] ?? '' }}</span> né(e) le <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 100px;">{{ $team['members'][0]['birth_date'] ?? '' }}</span> à <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 100px;">{{ $team['members'][0]['birth_place'] ?? '' }}</span></p>
            <p>Année universitaire : <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 150px;">{{ $defense['academic_year'] ?? '2023/2024' }}</span></p>
        </div>

        <!-- Academic Information -->
        <div class="mb-3 small-text">
            <p><strong>Domaine :</strong> Mathématique et Informatique</p>
            <p><strong>Filière :</strong> Informatique</p>
            <p><strong>Spécialité :</strong> <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 200px;">{{ $team['members'][0]['speciality'] ?? '' }}</span></p>
            <p><strong>Type de diplôme :</strong> Professionnelle.</p>
        </div>

        <!-- Jury Members -->
        <div class="mb-3 small-text">
            <p><strong>Devant le Jury composé de :</strong></p>
            <table class="table table-bordered-custom">
                <thead>
                    <tr>
                        <th class="table-header"></th>
                        <th class="table-header">Membre du Jury (Nom / Prénom)</th>
                        <th class="table-header">Grade</th>
                        <th class="table-header">Qualité</th>
                        <th class="table-header">Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($jury) && count($jury) > 0)
                        @foreach($jury as $index => $member)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $member['name'] ?? '' }}</td>
                            <td>{{ $member['grade'] ?? '' }}</td>
                            <td>{{ $member['role'] ?? '' }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    @else
                        @for($i = 1; $i <= 3; $i++)
                        <tr>
                            <td>{{ $i }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $i == 1 ? 'Président' : ($i == 2 ? 'Examinateur' : 'Encadreur') }}</td>
                            <td></td>
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Subject -->
        <div class="mb-3 small-text">
            <p><strong>Intitulé du sujet :</strong></p>
            <p>«<span style="border-bottom: 1px solid #000; display: inline-block; min-width: 75%;">{{ $project['title'] ?? '' }}</span>.</p>
            <p class="text-center"><span style="border-bottom: 1px solid #000; display: inline-block; min-width: 75%;"></span>».</p>
        </div>

        <!-- Results -->
        <div class="mb-3 small-text">
            <p><strong>Résultats de la délibération :</strong></p>
            <p><strong>Notes attribuées par le Jury :</strong></p>
            <table class="table table-bordered-custom">
                <thead>
                    <tr>
                        <th class="table-header">Manuscrit (6/8)</th>
                        <th class="table-header">Exposé oral (4/6)</th>
                        <th class="table-header">Réponses aux questions (5/6)</th>
                        <th class="table-header">Réalisation (5/-)</th>
                        <th class="table-header">Note finale du PFE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $defense['manuscript_grade'] ?? '' }}</td>
                        <td>{{ $defense['oral_grade'] ?? '' }}</td>
                        <td>{{ $defense['questions_grade'] ?? '' }}</td>
                        <td>{{ $defense['realization_grade'] ?? '' }}</td>
                        <td>{{ $defense['final_grade'] ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Decision -->
        <div class="mb-3 small-text">
            <p>Par conséquent, l'étudiant(e) est déclaré(e) :</p>
            <div class="d-flex">
                <div class="checkbox-container">
                    <input type="checkbox" id="admis" @if(isset($defense['final_grade']) && $defense['final_grade'] >= 10) checked @endif>
                    <label for="admis"><strong>Admis(e)</strong></label>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" id="ajourne" @if(isset($defense['final_grade']) && $defense['final_grade'] < 10) checked @endif>
                    <label for="ajourne"><strong>Ajourné(e)</strong></label>
                </div>
            </div>
            <p>à l'examen de soutenance de son Mémoire de Master avec la mention :</p>
            <div class="d-flex flex-wrap">
                <div class="checkbox-container">
                    <input type="checkbox" id="excellent" @if(isset($defense['final_grade']) && $defense['final_grade'] >= 18) checked @endif>
                    <label for="excellent">Excellent</label>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" id="tres-bien" @if(isset($defense['final_grade']) && $defense['final_grade'] >= 16 && $defense['final_grade'] < 18) checked @endif>
                    <label for="tres-bien">Très Bien</label>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" id="bien" @if(isset($defense['final_grade']) && $defense['final_grade'] >= 14 && $defense['final_grade'] < 16) checked @endif>
                    <label for="bien">Bien</label>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" id="assez-bien" @if(isset($defense['final_grade']) && $defense['final_grade'] >= 12 && $defense['final_grade'] < 14) checked @endif>
                    <label for="assez-bien">Assez Bien</label>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" id="passable" @if(isset($defense['final_grade']) && $defense['final_grade'] >= 10 && $defense['final_grade'] < 12) checked @endif>
                    <label for="passable">Passable</label>
                </div>
            </div>
            <div class="d-flex flex-wrap mt-1">
                <div class="checkbox-container">
                    <span>(18≤N≤20)</span>
                </div>
                <div class="checkbox-container">
                    <span>(16≤N<18)</span>
                </div>
                <div class="checkbox-container">
                    <span>(14≤N<16)</span>
                </div>
                <div class="checkbox-container">
                    <span>(12≤N<14)</span>
                </div>
                <div class="checkbox-container">
                    <span>(10≤N<12)</span>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-section row small-text">
            <div class="col-6 text-center">
                <p>Le Président du Jury</p>
            </div>
            <div class="col-6 text-center">
                <p>Le Chef de Département</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>