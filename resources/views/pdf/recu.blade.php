<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @include('pdf.partials.styles')
    <style>
        .montant { text-align: center; margin: 22px 0; padding: 14px; border: 1px solid #A7F3D0; background: #ECFDF5; }
        .montant .valeur { font-size: 26px; font-weight: bold; color: #059669; }
        .montant .lettres { font-size: 10px; color: #047857; margin-top: 2px; }
        .statut { display: inline-block; margin-top: 6px; padding: 3px 10px; background: #D1FAE5; color: #059669; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
    @php
        $candidature = $paiement->candidature;
        $libelles = ['inscription' => "Frais d'inscription au concours", 'visite_medicale' => 'Frais de visite médicale'];
    @endphp

    @include('pdf.partials.entete', [
        'titre' => 'Reçu de paiement',
        'sousTitre' => 'Reçu n° '.str_pad($paiement->id, 6, '0', STR_PAD_LEFT),
    ])

    <table class="info">
        <tr><td class="label">Reçu de</td><td class="value">{{ $candidature->candidat->name }}</td></tr>
        <tr><td class="label">N° de dossier</td><td class="value">{{ str_pad($candidature->id, 5, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td class="label">Concours</td><td class="value">{{ $candidature->concours->nom }}</td></tr>
        <tr><td class="label">Objet</td><td class="value">{{ $libelles[$paiement->type] ?? ucfirst(str_replace('_', ' ', $paiement->type)) }}</td></tr>
        <tr><td class="label">Mode de paiement</td><td class="value">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td></tr>
        <tr><td class="label">Référence de transaction</td><td class="value">{{ $paiement->reference_transaction }}</td></tr>
        <tr><td class="label">Date</td><td class="value">{{ $paiement->date_paiement?->format('d/m/Y à H:i') }}</td></tr>
    </table>

    <div class="montant">
        <div class="valeur">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</div>
        <span class="statut">PAIEMENT VALIDÉ</span>
    </div>

    <div class="encadre">
        Les frais d'inscription et de visite médicale ne sont pas remboursables, y compris en cas de non-admission
        ou de désistement. Conservez ce reçu : il peut être exigé lors du dépôt du dossier physique et pour
        toute réclamation (fonction « Signaler un problème » de votre espace candidat).
    </div>

    <div class="footer">
        Reçu généré le {{ now()->format('d/m/Y à H:i') }} par Concours-Pro · à conserver comme preuve de paiement.
    </div>
</body>
</html>
