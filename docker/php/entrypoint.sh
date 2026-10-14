#!/bin/sh
set -eu

if [ "${DB_CONNECTION:-}" = "mysql" ]; then
    until nc -z "${DB_HOST:-db}" "${DB_PORT:-3306}"; do
        echo "Waiting for MySQL..."
        sleep 2
    done
fi

php artisan migrate --force
php artisan storage:link || true

exec "$@"
