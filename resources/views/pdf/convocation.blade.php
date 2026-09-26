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
        table.info td.label { color: #475569; width: 40%; }
        table.info td.value { font-weight: bold; }
        .qr-box { text-align: center; margin-top: 30px; padding: 20px; border: 1px dashed #94A3B8; }
        .qr-box p { font-size: 11px; color: #475569; margin-top: 10px; }
        .footer { margin-top: 40px; font-size: 10px; color: #94A3B8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIGEC — IPNETP</h1>
        <p>Convocation officielle au concours</p>
    </div>

    <table class="info">
        <tr><td class="label">Candidat</td><td class="value">{{ $candidature->candidat->name }}</td></tr>
        <tr><td class="label">Concours</td><td class="value">{{ $candidature->concours->nom }}</td></tr>
        <tr><td class="label">Cycle / Filière</td><td class="value">{{ $candidature->concours->cycle }} · {{ $candidature->concours->filiere }}</td></tr>
        <tr><td class="label">Date du concours</td><td class="value">{{ $candidature->concours->date_concours?->format('d/m/Y') ?? 'À déterminer' }}</td></tr>
        <tr><td class="label">Numéro de dossier</td><td class="value">{{ $candidature->id }}</td></tr>
    </table>

    <div class="qr-box">
        <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="180" height="180">
        <p>Ce QR Code permet aux surveillants de vérifier instantanément l'identité du candidat le jour du concours.</p>
    </div>

    <div class="footer">
        Document généré automatiquement par SIGEC — toute falsification est passible de poursuites.
    </div>
</body>
</html>
