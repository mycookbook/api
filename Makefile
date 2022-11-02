COMPOSER=composer

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

setup: composer .env
	#install composer dependencies, copy .env.example, generate app key

composer:
	@echo "Hello World"

.env:
	cp .env.example .env

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