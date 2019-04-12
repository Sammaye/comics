#!/usr/bin/env bash

sudo find storage -type d -exec chgrp www-data {} +
sudo find storage -type d -exec chmod g+s {} +

composer install --optimize-autoloader --no-dev
npm install
npm run production
php artisan config:cache
php artisan route:cache
composer dump-autoload
