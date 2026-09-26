<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #0F172A; font-size: 13px; }
        .header { border-bottom: 3px solid #1E3A8A; padding-bottom: 12px; margin-bottom: 24px; }
        .header h1 { color: #1E3A8A; font-size: 20px; margin: 0 0 4px; }
        .header p { color: #475569; margin: 0; font-size: 11px; }
        h2 { font-size: 14px; color: #1E3A8A; margin-top: 24px; }
        table.info { width: 100%; margin-bottom: 8px; }
        table.info td { padding: 6px 0; border-bottom: 1px solid #E2E8F0; }
        table.info td.label { color: #475569; width: 45%; }
        table.info td.value { font-weight: bold; }
        .footer { margin-top: 40px; font-size: 10px; color: #94A3B8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIGEC — IPNETP</h1>
        <p>Fiche de candidature</p>
    </div>

    <h2>Identité du candidat</h2>
    <table class="info">
        <tr><td class="label">Nom</td><td class="value">{{ $candidature->candidat->name }}</td></tr>
        <tr><td class="label">Email</td><td class="value">{{ $candidature->candidat->email }}</td></tr>
        <tr><td class="label">Téléphone</td><td class="value">{{ $candidature->candidat->telephone ?? '—' }}</td></tr>
        <tr><td class="label">Date de naissance</td><td class="value">{{ $candidature->candidat->date_naissance?->format('d/m/Y') ?? '—' }}</td></tr>
    </table>

    <h2>Concours visé</h2>
    <table class="info">
        <tr><td class="label">Concours</td><td class="value">{{ $candidature->concours->nom }}</td></tr>
        <tr><td class="label">Cycle / Filière</td><td class="value">{{ $candidature->concours->cycle }} · {{ $candidature->concours->filiere }}</td></tr>
        <tr><td class="label">Diplôme déclaré</td><td class="value">{{ $candidature->diplome_candidat }}</td></tr>
    </table>

    <h2>Suivi du dossier</h2>
    <table class="info">
        <tr><td class="label">Numéro de dossier</td><td class="value">{{ $candidature->id }}</td></tr>
        <tr><td class="label">Statut</td><td class="value">{{ ucfirst(str_replace('_', ' ', $candidature->statut)) }}</td></tr>
        <tr><td class="label">Date de soumission</td><td class="value">{{ $candidature->date_soumission?->format('d/m/Y') }}</td></tr>
    </table>

    <div class="footer">
        Document généré automatiquement par SIGEC — fiche récapitulative de la candidature.
    </div>
</body>
</html>
