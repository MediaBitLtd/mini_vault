#!/bin/sh
set -e

composer install

if [ ! -e /var/www/data/database.sqlite ]; then
    touch /var/www/data/database.sqlite
    php artisan passport:keys || true
    php artisan key:generate || true
    php artisan migrate:fresh --seed --force
else
    php artisan migrate --force
fi

php artisan config:cache

npm install
npm run build

# Run the default command
$@
