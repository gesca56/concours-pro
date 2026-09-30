<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @include('pdf.partials.styles')
    <style>
        table.corps { width: 100%; }
        table.corps td { vertical-align: top; }
        .qr { text-align: center; border: 1px dashed #94A3B8; padding: 12px; }
        .qr p { font-size: 9.5px; color: #475569; margin: 6px 0 0; }
        ol.consignes { margin: 6px 0 0 16px; padding: 0; font-size: 11px; }
        ol.consignes li { margin-bottom: 4px; }
    </style>
</head>
<body>
    @php
        $concours = $candidature->concours;
        $cycle = config('ipnetp.cycles.'.$concours->cycle);
    @endphp

    @include('pdf.partials.entete', [
        'titre' => 'Convocation aux épreuves écrites',
        'sousTitre' => 'Concours direct d\'entrée — session '.($concours->date_concours ?? $concours->date_ouverture)->year,
    ])

    <table class="corps">
        <tr>
            <td style="width: 66%; padding-right: 16px;">
                <table class="info">
                    <tr><td class="label">Nom et prénoms</td><td class="value">{{ $candidature->candidat->name }}</td></tr>
                    <tr><td class="label">Date de naissance</td><td class="value">{{ $candidature->candidat->date_naissance?->format('d/m/Y') ?? '—' }}</td></tr>
                    <tr><td class="label">N° de dossier</td><td class="value">{{ str_pad($candidature->id, 5, '0', STR_PAD_LEFT) }}</td></tr>
                    <tr><td class="label">Concours</td><td class="value">{{ $concours->cycle }} — {{ $cycle['intitule'] ?? '' }}</td></tr>
                    <tr><td class="label">Spécialité</td><td class="value">{{ $concours->nom }}</td></tr>
                    <tr><td class="label">Date des écrits</td><td class="value">{{ $concours->date_concours?->translatedFormat('l j F Y') ?? 'Communiquée par voie d\'affichage' }}</td></tr>
                    <tr><td class="label">Centre</td><td class="value">IPNETP, {{ config('ipnetp.institut.ville') }} (sauf avis contraire)</td></tr>
                </table>
            </td>
            <td style="width: 34%;">
                <div class="qr">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="150" height="150">
                    <p>Code de contrôle à présenter à l'entrée de la salle.</p>
                </div>
            </td>
        </tr>
    </table>

    <h2>Programme des épreuves</h2>
    <table class="grille">
        <tr><th>Phase</th><th>Épreuve</th><th>Durée</th><th>Coef.</th></tr>
        @foreach (config('ipnetp.epreuves') as $epreuve)
            <tr>
                <td>{{ $epreuve['phase'] }}</td>
                <td>{{ $epreuve['nom'] }}</td>
                <td>
                    @if ($epreuve['nom'] === 'Épreuve de spécialité')
                        {{ $cycle['duree_specialite'] ?? '—' }}
                    @else
                        {{ $epreuve['duree'] ?? '—' }}
                    @endif
                </td>
                <td>{{ $epreuve['coefficient'] }}</td>
            </tr>
        @endforeach
    </table>
    <p style="font-size: 10px; color: #475569; margin-top: 4px;">L'entretien oral est réservé aux candidats déclarés admissibles à l'issue des épreuves écrites.</p>

    <h2>Consignes aux candidats</h2>
    <ol class="consignes">
        <li>Se présenter au moins 30 minutes avant le début des épreuves, muni(e) de la présente convocation et de sa pièce d'identité originale.</li>
        <li>Aucun candidat n'est admis en salle après l'ouverture des enveloppes de sujets.</li>
        <li>Les téléphones et tout appareil de communication doivent être éteints et rangés ; leur détention en salle constitue une fraude.</li>
        <li>Les copies sont anonymes : n'y porter ni nom, ni signature, ni signe distinctif.</li>
        <li>Toute fraude ou tentative de fraude entraîne l'exclusion du concours, sans préjudice de poursuites.</li>
    </ol>

    <div class="footer">
        Convocation n° {{ str_pad($candidature->id, 5, '0', STR_PAD_LEFT) }} générée le {{ now()->format('d/m/Y à H:i') }} par Concours-Pro · authenticité vérifiable par QR Code · toute falsification est passible de poursuites.
    </div>
</body>
</html>
