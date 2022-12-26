.RECIPEPREFIX +=
.DEFAULT_GOAL := help
.PHONY: *

help:
	@printf "\033[33mUsage:\033[0m\n  make [target] [arg=\"val\"...]\n\n\033[33mTargets:\033[0m\n"
	@grep -E '^[-a-zA-Z0-9_\.\/]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2}'

install:  copy_env build up setup

down: ## Destroy the containers
	@docker-compose down

build: ## build the containers
	@docker-compose build

db_seed: ## seed the database
	@php artisan db:seed

db_migrate: ## run db migrations
	@php artisan migrate

db_schemefy: ## Display the db schema in table format
	 @php artisan schema:show

setup: composer generate_key

copy_env: #todo: figure out a way to not override env vars if file_exists already
	@cp .env.example .env

reset_env: ## resets the env file from .env.example
	@cp .env.example .env
	@echo ".env reset!"

composer: ## Install project dependencies
	@docker-compose exec app composer install

generate_key: ## Generate APP_KEY and set in .env
	@docker-compose exec app php artisan key:generate

login: ## Creates a new user/token or generate new token for given user
	@php artisan auth:token

test_unit: ## Run unit testsuite
	@php vendor/bin/phpunit --testsuite=Unit

test_api: ## Run Api tests
	@php vendor/bin/phpunit --testsuite=Api

test: ## Run the entire test suites
	@php vendor/bin/phpunit tests/

shell_app: ## ssh into the app container
	@docker-compose exec app /bin/bash

shell_db: ## ssh into the database container
	@docker-compose exec db /bin/bash

clear: clear_cache clear_views clear_routes dump_autoload

clear_cache:
	@php artisan cache:clear

clear_views:
	@php artisan view:clear

clear_routes:
	@php artisan route:clear

dump_autoload: ## Composer dumpautoload
	@composer dumpautoload

up: ## Restarts and provisions the containers
	@docker-compose up

docker_prune: prune_images prune_volumes prune_containers

prune_images: ## Remove dangling images and free up space
	@docker image prune

prune_containers: ## Remove the containers
	@docker container prune

prune_volumes: ## Removes dangling volumes
	@docker volume prune

