#!/bin/sh
set -e

# Les erreurs applicatives vont dans la sortie standard : elles apparaissent
# dans l'onglet « Logs » de Render (le fichier storage/logs y est invisible).
export LOG_CHANNEL=stderr

php artisan config:clear

# Contrôle explicite de la base avant de démarrer (message lisible dans les logs).
if php artisan db:show --counts > /dev/null 2>&1; then
    echo "[concours-pro] Connexion à la base de données : OK"
else
    echo "[concours-pro] ERREUR : base de données injoignable — détail ci-dessous"
    php artisan db:show 2>&1 | tail -5 || true
fi
php artisan migrate --force
php artisan db:seed --class=ComptesDemoSeeder --force

if [ "$RUN_SEEDER" = "true" ]; then
    php artisan db:seed --force
fi

php artisan storage:link || true

# Tâches planifiées (clôture automatique des concours expirés) : pas de cron sur Render,
# on exécute la vérification au démarrage puis le planificateur en arrière-plan.
php artisan app:cloture-concours-expires || true
php artisan schedule:work > /dev/null 2>&1 &

exec apache2-foreground
