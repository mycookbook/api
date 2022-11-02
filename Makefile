install: setup down build up help

down:
	docker-compose down

build: #build the containers
	docker-compose build
up:
	docker-compose up

help: #topic
	@echo "Help topics"

provision:
	@echo "Hello World"

db-seed:
	@echo "Hello World"

migrate:
	@echo "Hello World"

setup: copy-env composer generate-key
	#generate app key

copy-env:
	cp .env.example .env

composer:
	composer install

generate-key:
	php artisan key:generate

login:
	@echo "Hello World"
test-unit:
	@echo "Hello World"
test-functional:
	@echo "Hello World"
test:
	@echo "Hello World"

clean:
	@echo "Cleaning up..."

shell:
	@echo "ssh into the app container."