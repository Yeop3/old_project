#!/usr/bin/env sh

set -e

while [ true ]
do
    php /app/main/artisan schedule:run --verbose --no-interaction &
    sleep 60
done
