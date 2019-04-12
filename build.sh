#!/usr/bin/env bash

composer install --optimize-autoloader --no-dev
npm install
npm run production
php artisan config:cache
php artisan route:cache
