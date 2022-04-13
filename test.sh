#!/bin/bash -ex

# safeguard
grep DB_CONNECTION=sqlite .env > /dev/null

touch database/database.sqlite
php artisan migrate:refresh
php artisan db:seed --class=TestingSeeder

vendor/bin/phpunit
