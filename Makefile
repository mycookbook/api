install: setup down build up help

down: #destroy the containers
	docker-compose down

prune:
	@echo "Prunning containers and volumes"

build: #build the containers
	docker-compose build

up: #start the containers
	docker-compose up

clean: down prune

force-clean:
	@echo "force cleaning."

db-seed: #seed the database
	php artisan db:seed

migrate: #run db migrations
	php artisan migrate

setup: copy-env composer generate-key

copy-env:
	cp .env.example .env

composer:
	composer install

generate-key:
	php artisan key:generate

login: #todo: create an artisan cmd
	@echo "Hello World"

test-unit:
	php vendor/bin/phpunit --testsuite=Unit

test-functional:
	php vendor/bin/phpunit --testsuite=Functional

test:
	php vendor/bin/phpunit tests/

app-shell: #ssh into the app container
	docker-compose exec app /bin/bash

db-shell: #ssh into the database container
	docker-compose exec db /bin/bash

help: #topic
	@echo "Help topics"
