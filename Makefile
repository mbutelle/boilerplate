.DEFAULT_GOAL  := help
DOCKER         = docker
DOCKER_COMPOSE = docker-compose

FRONT = front
PHP = php

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
.PHONY: ps

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## /[33m/'
.PHONY: help

##
## Container's connection commands
## -------
##

front-shell: ## open shell in front container
	$(DOCKER_COMPOSE) exec $(FRONT) sh
.PHONY: stop

php-shell: start ## open shell in php container
	$(DOCKER_COMPOSE) exec -u www-data $(PHP) bash
.PHONY: stop

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

php-cs-fixer: ## php cs fixer
	${DOCKER} run --init -it --rm -v "$(pwd)/api:/project" -w /project jakzal/phpqa phpcs .
.PHONY: php-cs-fixer
