#!/bin/sh
set -e

php artisan config:clear
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
