.DEFAULT_GOAL  := help
DOCKER         = docker
DOCKER_COMPOSE = docker compose

# Le service PHP s'appelle désormais `api` : c'est la même image FrankenPHP qui
# sert le HTTP et exécute les commandes console, il n'y a plus de conteneur
# nginx séparé.
API   = api
FRONT = front

##
## Commandes courantes
## -------
##

start: env ## démarrer le projet
	$(DOCKER_COMPOSE) up -d --remove-orphans
.PHONY: start

stop: ## arrêter le projet
	$(DOCKER_COMPOSE) stop
.PHONY: stop

ps: ## lister les conteneurs
	$(DOCKER_COMPOSE) ps
.PHONY: ps

logs: ## suivre les journaux
	$(DOCKER_COMPOSE) logs -f
.PHONY: logs

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## /[33m/'
.PHONY: help

##
## Droits et environnement
## -------
##

env: ## écrire les UID/GID hôte dans .env pour que les conteneurs tournent sous votre identité
	@grep -q '^UID=' .env 2>/dev/null || echo "UID=$$(id -u)" >> .env
	@grep -q '^GID=' .env 2>/dev/null || echo "GID=$$(id -g)" >> .env
	@echo "UID/GID écrits dans .env : $$(id -u):$$(id -g)"
.PHONY: env

fix-perms: ## reprendre la propriété des fichiers laissés en root par un conteneur (filet de sécurité)
	# -h : modifier le lien lui-même, jamais sa cible (dist/ peut pointer dans le vide).
	sudo find . -path ./mysql_storage -prune -o -path ./.git -prune -o \
		\( ! -user $$(id -u) -o ! -group $$(id -g) \) -exec chown -h $$(id -u):$$(id -g) {} +
	@echo "Droits rendus à $$(id -u):$$(id -g) (mysql_storage et .git exclus)"
.PHONY: fix-perms

##
## Accès aux conteneurs
## -------
##

front-shell: ## ouvrir un shell dans le conteneur front
	$(DOCKER_COMPOSE) exec $(FRONT) sh
.PHONY: front-shell

api-shell: ## ouvrir un shell dans le conteneur api
	$(DOCKER_COMPOSE) exec $(API) sh
.PHONY: api-shell

##
## Utilitaires
## -------
##

fixture-load: ## charger les fixtures
	$(DOCKER_COMPOSE) exec $(API) bin/console doctrine:fixtures:load --no-interaction
.PHONY: fixture-load

migrate: ## jouer les migrations
	$(DOCKER_COMPOSE) exec $(API) bin/console doctrine:migrations:migrate --no-interaction
.PHONY: migrate

##
## Tests
## -------
##

test: php-test front-test ## lancer tous les tests
.PHONY: test

php-test: ## lancer les tests Pest
	$(DOCKER_COMPOSE) exec $(API) ./vendor/bin/pest
.PHONY: php-test

php-test-parallel: ## lancer les tests Pest en parallèle
	$(DOCKER_COMPOSE) exec $(API) ./vendor/bin/pest --parallel
.PHONY: php-test-parallel

php-test-coverage: ## lancer les tests Pest avec couverture
	$(DOCKER_COMPOSE) exec $(API) /bin/sh -c "export XDEBUG_MODE=coverage && ./vendor/bin/pest --coverage"
.PHONY: php-test-coverage

front-test: ## lancer les tests du front
	$(DOCKER_COMPOSE) exec $(FRONT) pnpm test
.PHONY: front-test

##
## Technique
## -------
##

sdk: ## exporter l'OpenAPI puis régénérer le SDK partagé
	$(DOCKER_COMPOSE) exec $(API) bin/console api:openapi:export -o /var/shared/openapi_schema.json
	$(DOCKER_COMPOSE) exec $(FRONT) pnpm --filter @bp/api-client orval
.PHONY: sdk

##
## Analyse de code
## -------
##

phpstan: ## lancer PHPStan (le niveau vient de phpstan.dist.neon, pas d'un drapeau)
	$(DOCKER_COMPOSE) exec $(API) php -dmemory_limit=-1 vendor/bin/phpstan analyse
.PHONY: phpstan

typecheck: ## vérifier les types du front
	$(DOCKER_COMPOSE) exec $(FRONT) pnpm typecheck
.PHONY: typecheck

##
## Linters
## -------
##

linter: php-linter front-linter ## tous les linters
.PHONY: linter

linter-fix: php-linter-fix front-linter-fix ## corriger avec tous les linters
.PHONY: linter-fix

php-linter: ## php-cs-fixer
	$(DOCKER_COMPOSE) exec $(API) vendor/bin/php-cs-fixer check
.PHONY: php-linter

php-linter-fix: ## php-cs-fixer, en correction
	$(DOCKER_COMPOSE) exec $(API) vendor/bin/php-cs-fixer fix
.PHONY: php-linter-fix

front-linter: ## eslint sur le front
	$(DOCKER_COMPOSE) exec $(FRONT) pnpm lint
.PHONY: front-linter

front-linter-fix: ## eslint sur le front, en correction
	$(DOCKER_COMPOSE) exec $(FRONT) pnpm lint:fix
.PHONY: front-linter-fix

##
## Intégration continue
## -------
##

check-php-coverage: ## vérifier le seuil de couverture PHP
	$(DOCKER_COMPOSE) exec $(API) /bin/sh -c "export XDEBUG_MODE=coverage && ./vendor/bin/pest --coverage --min=80"
.PHONY: check-php-coverage

build-api: ## construire l'image applicative (ENV=staging|production)
	$(DOCKER) build ./api --target=api-base --tag boilerplate-api:$(or $(ENV),latest)
.PHONY: build-api

build-front: ## construire le front (ENV=staging|production)
	$(DOCKER_COMPOSE) run --rm --no-deps $(FRONT) pnpm build
.PHONY: build-front
