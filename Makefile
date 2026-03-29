.PHONY: help up down build shell logs composer console db-migrate db-reset

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Start all containers (detached)
	docker compose up -d

down: ## Stop and remove containers
	docker compose down

build: ## Rebuild images
	docker compose build --no-cache

shell: ## Open a bash shell in the app container
	docker compose exec app bash

logs: ## Tail logs
	docker compose logs -f

composer: ## Run composer (e.g. make composer cmd="require package/name")
	docker compose exec app composer $(cmd)

console: ## Run Symfony console (e.g. make console cmd="cache:clear")
	docker compose exec app php bin/console $(cmd)

db-migrate: ## Run Doctrine migrations
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

db-reset: ## Drop, create, migrate, and load fixtures
	docker compose exec app php bin/console doctrine:database:drop --force --no-interaction || true
	docker compose exec app php bin/console doctrine:database:create --no-interaction
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec app php bin/console doctrine:fixtures:load --no-interaction
