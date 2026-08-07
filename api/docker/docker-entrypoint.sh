#!/bin/sh
set -e

composer install --apcu-autoloader -a --no-scripts

# Paire de clés JWT au premier démarrage ; sans effet une fois qu'elle existe.
bin/console lexik:jwt:generate-keypair --skip-if-exists --no-interaction

bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec docker-php-entrypoint "$@"
