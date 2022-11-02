.RECIPEPREFIX +=
.DEFAULT_GOAL := help

help:
	@printf "\033[33mUsage:\033[0m\n  make [target] [arg=\"val\"...]\n\n\033[33mTargets:\033[0m\n"
	@grep -E '^[-a-zA-Z0-9_\.\/]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2}'

install: ## Destroy the containers, restarts them
	down build up

down: ## Destroy the containers
	docker-compose down

build: ## build the containers
	docker-compose build

db_seed: ## seed the database
	php artisan db:seed

db_migrate: ## run db migrations
	php artisan migrate

setup: ## Setup the app
	copy_env composer generate_key

copy_env:
	cp .env.example .env

composer: ## Install project dependencies
	composer install

generate_key: ## Generate APP_KEY and set in .env
	php artisan key:generate

login: #todo: create an artisan cmd
	@echo "Hello World"

test_unit: ## Run unit testsuite
	php vendor/bin/phpunit --testsuite=Unit

test_api: ## Run Api tests
	php vendor/bin/phpunit --testsuite=Api

test: ## Run the entire test suites
	php vendor/bin/phpunit tests/

app_shell: ## ssh into the app container
	docker-compose exec app /bin/bash

db_shell: ## ssh into the database container
	docker-compose exec db /bin/bash

clear: ## Clears the cache
	clear_cache clear_views clear_routes dump_autoload

clear_cache:
	php artisan cache:clear

clear_views:
	php artisan view:clear

clear_routes:
	php artisan route:clear

dump_autoload: ## Composer dumpautoload
	composer dumpautoload

up: ## Restarts and provisions the containers
	docker-compose up

force_prune: ## Prunes containers, images and volumes
	prune_images prune_containers prune_volumes

prune_images: ## Remove dangling images and free up space
	docker image prune

prune_containers: ## Remove the containers
	docker container prune

prune_volumes: ## Removes dangling volumes
	docker volume prune
