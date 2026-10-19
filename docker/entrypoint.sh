#!/bin/sh
set -e

# The APK volume is mounted at runtime and may come up owned by root.
mkdir -p storage/app/apk
chown -R www-data:www-data storage

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

exec supervisord -c /etc/supervisord.conf
