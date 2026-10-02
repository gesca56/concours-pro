<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @include('pdf.partials.styles')
    <style>
        .notes { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 11px; }
        .notes th { background: #1E3A8A; color: #fff; padding: 6px 8px; text-align: left; font-weight: bold; }
        .notes td { padding: 6px 8px; border-bottom: 1px solid #E5E7EB; }
        .notes .nombre { text-align: right; }
        .notes tfoot td { font-weight: bold; background: #F1F5F9; border-top: 2px solid #1E3A8A; }
        .synthese { margin-top: 18px; padding: 12px; border: 1px solid #BFDBFE; background: #EFF6FF; text-align: center; }
        .synthese .valeur { font-size: 22px; font-weight: bold; color: #1E3A8A; }
        .signature { margin-top: 36px; width: 100%; font-size: 11px; }
        .signature td { width: 50%; vertical-align: top; }
    </style>
</head>
<body>
    @include('pdf.partials.entete', [
        'titre' => 'Bulletin de notes',
        'sousTitre' => 'Année académique '.$promotion->annee_academique,
        'service' => 'Direction des études',
    ])

    <table class="info">
        <tr><td class="label">Élève-professeur</td><td class="value">{{ $eleve->name }}</td></tr>
        <tr><td class="label">Matricule</td><td class="value">{{ $matricule }}</td></tr>
        <tr><td class="label">Promotion</td><td class="value">{{ $promotion->nom }}</td></tr>
        <tr><td class="label">Cycle</td><td class="value">{{ $promotion->cycle }} — {{ config('ipnetp.cycles.'.$promotion->cycle.'.intitule') }}</td></tr>
    </table>

    <table class="notes">
        <thead>
            <tr>
                <th>Module</th>
                <th class="nombre">Coef.</th>
                <th class="nombre">Moyenne /20</th>
                <th class="nombre">Points</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($releve['modules'] as $ligne)
                <tr>
                    <td>{{ $ligne['module']->titre }}@if ($ligne['module']->enseignant) <span style="color:#6B7280">— {{ $ligne['module']->enseignant->name }}</span>@endif</td>
                    <td class="nombre">{{ $ligne['module']->coefficient }}</td>
                    <td class="nombre">{{ $ligne['moyenne'] !== null ? number_format($ligne['moyenne'], 2, ',', ' ') : 'NC' }}</td>
                    <td class="nombre">{{ $ligne['moyenne'] !== null ? number_format($ligne['moyenne'] * $ligne['module']->coefficient, 2, ',', ' ') : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total des coefficients notés</td>
                <td class="nombre">{{ collect($releve['modules'])->whereNotNull('moyenne')->sum(fn ($l) => $l['module']->coefficient) }}</td>
                <td class="nombre">{{ $releve['moyenne'] !== null ? number_format($releve['moyenne'], 2, ',', ' ') : 'NC' }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="synthese">
        Moyenne générale : <span class="valeur">{{ $releve['moyenne'] !== null ? number_format($releve['moyenne'], 2, ',', ' ').' / 20' : 'non calculée' }}</span>
        @if ($releve['mention'])<br>Mention : <strong>{{ $releve['mention'] }}</strong>@endif
    </div>

    <table class="signature">
        <tr>
            <td>NC : non classé (aucune note dans le module).<br>La moyenne générale est pondérée par les coefficients.</td>
            <td style="text-align:center">Fait à Abidjan, le {{ now()->format('d/m/Y') }}<br>Le Directeur des études<br><br><br>&nbsp;</td>
        </tr>
    </table>

    <div class="footer">
        Bulletin provisoire généré le {{ now()->format('d/m/Y à H:i') }} par Concours-Pro · seul le relevé signé par la Direction des études fait foi.
    </div>
</body>
</html>
