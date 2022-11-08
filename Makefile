.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: tests
tests: ## Lance les tests
	docker compose exec php ./vendor/bin/pest --group=tests

.PHONY: coverage
coverage: ## Lance les tests
	XDEBUG_MODE=coverage ./vendor/bin/pest --group=tests --coverage

.PHONY: test-advanced
test-advanced: ## Lance les tests du group advanced
	docker compose exec php ./vendor/bin/pest --group=advanced

.PHONY: seed
seed: ## Seed la db
	docker compose exec php ./vendor/bin/pest --group=seed

.PHONY: migrate
migrate: ## Lance les migrations
	docker compose exec php ./vendor/bin/pest --group=migrate

.PHONY: start
start: ## Lance le serveur de développement sans watch les assets
	docker compose up -d

.PHONY: stop
stop:
	docker compose down

.PHONY: cs
cs: ## run php cs fixer
	docker compose exec php ./vendor/bin/php-cs-fixer fix

.PHONY: analyse
analyse: ## run phpstan analyse
	docker compose exec php ./vendor/bin/phpstan analyse

.PHONY: install
install: ## Installe les dépendances
	docker compose exec php composer install

.PHONY: ci-tests
ci-tests: ## Lance les tests pour la CI
	make install
	make migrate
	make seed
	make tests