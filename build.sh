#!/usr/bin/env bash

composer install --optimize-autoloader --no-dev
npm run production
php artisan config:cache
php artisan route:cache
