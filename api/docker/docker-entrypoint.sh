#!/bin/sh
set -e

# Sans classmap autoritaire ni cache APCu : le code est monté depuis le poste, et une
# classe arrivée avec une branche doit se trouver sans régénérer l'autoload. Avec une
# classmap autoritaire, les tests d'architecture ne voyaient même pas une classe
# ajoutée depuis le build.
composer install --no-scripts

# Paire de clés JWT au premier démarrage ; sans effet une fois qu'elle existe.
bin/console lexik:jwt:generate-keypair --skip-if-exists --no-interaction

bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec docker-php-entrypoint "$@"
