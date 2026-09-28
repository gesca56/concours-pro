#!/bin/sh
set -e

php artisan config:clear
php artisan migrate --force
php artisan db:seed --class=ComptesDemoSeeder --force

if [ "$RUN_SEEDER" = "true" ]; then
    php artisan db:seed --force
fi

php artisan storage:link || true

exec apache2-foreground
