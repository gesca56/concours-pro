<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @include('pdf.partials.styles')
    <style>
        table.pieces { width: 100%; font-size: 11px; }
        table.pieces td { padding: 3px 0; }
        table.signature { width: 100%; margin-top: 14px; font-size: 11px; }
        table.signature td { vertical-align: top; }
    </style>
</head>
<body>
    @php
        $concours = $candidature->concours;
        $cycle = config('ipnetp.cycles.'.$concours->cycle);
        $typesDeposes = $candidature->documents->where('statut_verification', '!=', 'rejete')->pluck('type')->all();
    @endphp

    @include('pdf.partials.entete', [
        'titre' => 'Fiche d\'inscription',
        'sousTitre' => 'Concours direct d\'entrée — à joindre au dossier physique',
    ])

    <h2>Identité du candidat</h2>
    <table class="info">
        <tr><td class="label">Nom et prénoms</td><td class="value">{{ $candidature->candidat->name }}</td></tr>
        <tr><td class="label">Date de naissance</td><td class="value">{{ $candidature->candidat->date_naissance?->format('d/m/Y') ?? '—' }}</td></tr>
        <tr><td class="label">Âge au 1er janvier {{ $concours->dateReferenceAge()->year }}</td><td class="value">{{ $candidature->candidat->date_naissance ? (int) $candidature->candidat->date_naissance->diffInYears($concours->dateReferenceAge()).' ans' : '—' }}</td></tr>
        <tr><td class="label">Téléphone</td><td class="value">{{ $candidature->candidat->telephone ?? '—' }}</td></tr>
        <tr><td class="label">Adresse e-mail</td><td class="value">{{ $candidature->candidat->email }}</td></tr>
    </table>

    <h2>Concours visé</h2>
    <table class="info">
        <tr><td class="label">Concours</td><td class="value">{{ $concours->cycle }} — {{ $cycle['intitule'] ?? '' }}</td></tr>
        <tr><td class="label">Spécialité / session</td><td class="value">{{ $concours->nom }}</td></tr>
        <tr><td class="label">Diplôme déclaré</td><td class="value">{{ $candidature->diplome_candidat }}</td></tr>
        <tr><td class="label">N° de dossier</td><td class="value">{{ str_pad($candidature->id, 5, '0', STR_PAD_LEFT) }} — préinscrit le {{ $candidature->date_soumission?->format('d/m/Y') }}</td></tr>
        @if ($cycle)
            <tr><td class="label">Chemise du dossier physique</td><td class="value"><span class="chemise" style="background: {{ $cycle['chemise']['hex'] }};"></span>{{ $cycle['chemise']['nom'] }}</td></tr>
        @endif
    </table>

    <h2>Pièces à joindre</h2>
    <table class="pieces">
        @foreach (config('ipnetp.pieces') as $piece)
            <tr>
                <td>
                    <span class="case" style="{{ $piece['type'] && in_array($piece['type'], $typesDeposes, true) ? 'background:#0F172A;' : '' }}"></span>
                    {{ $piece['libelle'] }}@if ($piece['note']) <span style="color:#64748B;">({{ $piece['note'] }})</span>@endif
                </td>
            </tr>
        @endforeach
    </table>
    <p style="font-size: 9.5px; color: #64748B; margin: 4px 0 0;">Case noircie : pièce déjà déposée en ligne. Les originaux ou copies légalisées restent exigés au dépôt physique.</p>

    <div class="encadre">
        Je soussigné(e) <strong>{{ $candidature->candidat->name }}</strong> certifie sur l'honneur l'exactitude des
        renseignements portés sur la présente fiche et m'engage, en cas d'admission, à servir dans tout établissement
        public d'enseignement technique et de formation professionnelle où je serai affecté(e).
        Toute fausse déclaration entraîne l'annulation de la candidature, même après admission.
    </div>

    <table class="signature">
        <tr>
            <td style="width: 50%;">Fait à ............................................, le ......../......../{{ now()->year }}</td>
            <td style="width: 50%; text-align: center;">Signature du candidat<br><br><br>..........................................</td>
        </tr>
    </table>

    <div class="footer">
        Fiche générée le {{ now()->format('d/m/Y à H:i') }} par Concours-Pro · dépôt au {{ config('ipnetp.institut.secretariat') }} · {{ config('ipnetp.institut.horaires') }}
    </div>
</body>
</html>
