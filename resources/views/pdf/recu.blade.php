<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #0F172A; font-size: 13px; }
        .header { border-bottom: 3px solid #1E3A8A; padding-bottom: 12px; margin-bottom: 24px; }
        .header h1 { color: #1E3A8A; font-size: 20px; margin: 0 0 4px; }
        .header p { color: #475569; margin: 0; font-size: 11px; }
        table.info { width: 100%; margin-bottom: 24px; }
        table.info td { padding: 6px 0; border-bottom: 1px solid #E2E8F0; }
        table.info td.label { color: #475569; width: 45%; }
        table.info td.value { font-weight: bold; }
        .montant { text-align: center; margin: 30px 0; }
        .montant .valeur { font-size: 28px; font-weight: bold; color: #059669; }
        .statut { display: inline-block; padding: 4px 12px; background: #D1FAE5; color: #059669; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .footer { margin-top: 40px; font-size: 10px; color: #94A3B8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIGEC — IPNETP</h1>
        <p>Reçu de paiement</p>
    </div>

    <table class="info">
        <tr><td class="label">Candidat</td><td class="value">{{ $paiement->candidature->candidat->name }}</td></tr>
        <tr><td class="label">Concours</td><td class="value">{{ $paiement->candidature->concours->nom }}</td></tr>
        <tr><td class="label">Type de paiement</td><td class="value">{{ ucfirst(str_replace('_', ' ', $paiement->type)) }}</td></tr>
        <tr><td class="label">Référence de transaction</td><td class="value">{{ $paiement->reference_transaction }}</td></tr>
        <tr><td class="label">Mode de paiement</td><td class="value">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td></tr>
        <tr><td class="label">Date</td><td class="value">{{ $paiement->date_paiement?->format('d/m/Y à H:i') }}</td></tr>
    </table>

    <div class="montant">
        <div class="valeur">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</div>
        <span class="statut">PAIEMENT VALIDÉ</span>
    </div>

    <div class="footer">
        Document généré automatiquement par SIGEC — à conserver comme preuve de paiement.
    </div>
</body>
</html>
