#!/bin/sh
set -eu

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] && [ "${1:-}" = "apache2-foreground" ]; then
    database_path="${DB_DATABASE:-/tmp/laravel.sqlite}"
    mkdir -p "$(dirname "$database_path")"
    touch "$database_path"
    chown www-data:www-data "$database_path"
    gosu www-data php artisan migrate --force
fi

exec "$@"
