.DEFAULT_GOAL  := help
DOCKER         = docker
DOCKER_COMPOSE = docker compose

FRONT = client
PHP = php

# code analysis config
PHPSTAN_LEVEL = 1

# build config
BUILD_PHP_CONTAINER_NAME=boilerplater_build_php
BUILD_PHP_PACKAGE_NAME=archive-php.tar.gz

BUILD_FRONT_CONTAINER_NAME=boilerplater_build_front
BUILD_FRONT_PACKAGE_NAME_STAGING=archive-admin-staging.tar.gz
BUILD_FRONT_PACKAGE_NAME_PRODUCTION=archive-admin-production.tar.gz

##
## Common Commands
## -------
##

start: ## Start the project
	$(DOCKER_COMPOSE) up -d --remove-orphans --force-recreate
.PHONY: start

stop: ## Stop the project
	$(DOCKER_COMPOSE) stop
.PHONY: stop

ps: ## list all containers
	$(DOCKER_COMPOSE) ps
.PHONY: ps

logs: ## watch container's logs
	$(DOCKER_COMPOSE) logs -f
.PHONY: logs

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## /[33m/'
.PHONY: help

##
## Container's connection commands
## -------
##

front-shell: ## open shell in front container
	$(DOCKER_COMPOSE) exec $(FRONT) sh
.PHONY: front-shell

php-shell: ## open shell in php container
	$(DOCKER_COMPOSE) exec $(PHP) bash
.PHONY: php-shell

##
## Utility command
## -------
##

fixture-load: ## load alice fixture
	$(DOCKER_COMPOSE) exec $(PHP) bin/console doctrine:fixtures:load
.PHONY: fixture-load

##
## Tests command
## -------
##

test: php-test ## launch all tests
.PHONY: test

php-test-parallel: ## launch pest tests with parallel
	$(DOCKER_COMPOSE) exec $(PHP) ./vendor/bin/pest --parallel
.PHONY: php-test-parallel

php-test-coverage: ## launch pest tests with coverage
	$(DOCKER_COMPOSE) exec $(PHP) /bin/bash -c "export XDEBUG_MODE=coverage && ./vendor/bin/pest --coverage"
.PHONY: php-test-coverage

php-test: ## launch pest tests
	$(DOCKER_COMPOSE) exec $(PHP) ./vendor/bin/pest
.PHONY: php-test

##
## Technical command
## -------
##

generate-front-services: ## use orval to generate http client
	$(DOCKER_COMPOSE) exec $(PHP) bin/console api:openapi:export -o /var/shared/openai_schema.json
	$(DOCKER_COMPOSE) exec $(FRONT) yarn orval --config ./orval.config.cjs
.PHONY: generate-front-services

##
## Code analysis
## -------
##

phpstan: ## launch phpstan on backend
	$(DOCKER_COMPOSE) exec $(PHP) php -dmemory_limit=-1 vendor/bin/phpstan --level=${PHPSTAN_LEVEL} analyse src/
.PHONY: phpstan

##
## Linter
## -------
##

linter: php-linter admin-linter ## Global linter
.PHONY: linter

linter-fix: php-linter-fix admin-linter-fix ## Global linter fix
.PHONY: linter-fix


php-linter: ## php cs fixer
	$(DOCKER_COMPOSE) exec $(PHP) vendor/bin/php-cs-fixer check
.PHONY: php-linter

php-linter-fix: ## php cs fixer fix
	$(DOCKER_COMPOSE) exec $(PHP) vendor/bin/php-cs-fixer fix
.PHONY: php-linter-fix

admin-linter: ## admin linter
	$(DOCKER_COMPOSE) exec $(FRONT) yarn lint
.PHONY: admin-linter

admin-linter-fix: ## admin linter fix
	$(DOCKER_COMPOSE) exec $(FRONT) yarn lint:fix
.PHONY: admin-linter-fix

##
## CI / CD
## -------
##

check-php-coverage: ## check php coverage
	$(DOCKER_COMPOSE) exec $(PHP) /bin/bash -c "export XDEBUG_MODE=coverage && ./vendor/bin/pest --coverage --min=80"
.PHONY: check-php-coverage

build-php: ## create php package
	$(DOCKER) rmi $(BUILD_PHP_CONTAINER_NAME) -f
	$(DOCKER) build backend --target=build_php --tag $(BUILD_PHP_CONTAINER_NAME)
	$(DOCKER) rm $(BUILD_PHP_CONTAINER_NAME) -f
	$(DOCKER) run -it --detach --name=$(BUILD_PHP_CONTAINER_NAME) $(BUILD_PHP_CONTAINER_NAME) bash
	$(DOCKER) exec $(BUILD_PHP_CONTAINER_NAME) tar czf $(BUILD_PHP_PACKAGE_NAME) \
		bin \
		config \
		src \
		vendor \
		templates \
		translations \
		migrations \
		composer.json \
		composer.lock \
		var/cache/prod \
		public/index.php \
		public/bundles
	mkdir -p build
	$(DOCKER) cp $(BUILD_PHP_CONTAINER_NAME):/srv/www/$(BUILD_PHP_PACKAGE_NAME) build/$(BUILD_PHP_PACKAGE_NAME)
	$(DOCKER) rm $(BUILD_PHP_CONTAINER_NAME) -f
	$(DOCKER) rmi $(BUILD_PHP_CONTAINER_NAME) -f
.PHONY: build-php

build-admin-staging: ## create admin package for staging
	$(DOCKER) rmi $(BUILD_FRONT_CONTAINER_NAME)_dev -f
	$(DOCKER) build admin --target=build_admin_staging --tag $(BUILD_FRONT_CONTAINER_NAME)_dev --no-cache
	$(DOCKER) rm $(BUILD_FRONT_CONTAINER_NAME)_dev -f
	$(DOCKER) run -it --detach --name=$(BUILD_FRONT_CONTAINER_NAME)_dev $(BUILD_FRONT_CONTAINER_NAME)_dev sh
	$(DOCKER) exec $(BUILD_FRONT_CONTAINER_NAME)_dev tar czf $(BUILD_FRONT_PACKAGE_NAME_STAGING) \
		.output/public
	mkdir -p build
	$(DOCKER) cp $(BUILD_FRONT_CONTAINER_NAME)_dev:/usr/src/project/admin/$(BUILD_FRONT_PACKAGE_NAME_STAGING) build/$(BUILD_FRONT_PACKAGE_NAME_STAGING)
	$(DOCKER) rm $(BUILD_FRONT_CONTAINER_NAME)_dev -f
	$(DOCKER) rmi $(BUILD_FRONT_CONTAINER_NAME)_dev -f
.PHONY: build-admin-preprod

build-admin-production: ## create admin package for production
	$(DOCKER) rmi $(BUILD_FRONT_CONTAINER_NAME)_dev -f
	$(DOCKER) build admin --target=build_admin_production --tag $(BUILD_FRONT_CONTAINER_NAME)_dev --no-cache
	$(DOCKER) rm $(BUILD_FRONT_CONTAINER_NAME)_dev -f
	$(DOCKER) run -it --detach --name=$(BUILD_FRONT_CONTAINER_NAME)_dev $(BUILD_FRONT_CONTAINER_NAME)_dev sh
	$(DOCKER) exec $(BUILD_FRONT_CONTAINER_NAME)_dev tar czf $(BUILD_FRONT_PACKAGE_NAME_PRODUCTION) \
		.output/public
	mkdir -p build
	$(DOCKER) cp $(BUILD_FRONT_CONTAINER_NAME)_dev:/usr/src/project/admin/$(BUILD_FRONT_PACKAGE_NAME_PRODUCTION) build/$(BUILD_FRONT_PACKAGE_NAME_PRODUCTION)
	$(DOCKER) rm $(BUILD_FRONT_CONTAINER_NAME)_dev -f
	$(DOCKER) rmi $(BUILD_FRONT_CONTAINER_NAME)_dev -f
.PHONY: build-admin-production
