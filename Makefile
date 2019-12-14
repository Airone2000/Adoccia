.PHONY: help
help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | cut -d: -f2- | sort -t: -k 2,2 | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# Tests
.PHONY: unit
unit: ## unit -- Execute unit test
	php vendor/bin/simple-phpunit

# Coding Style
.PHONY: cs
cs: ## cs -- Check code style
	php vendor/bin/php-cs-fixer fix -v --dry-run --using-cache=no

.PHONY: fix
fix: ## fix -- fix code style
	php vendor/bin/php-cs-fixer fix -v

# Project
.PHONY: install
install: ## install -- Install project dependencies
	composer install

.PHONY: start
start: ## start -- Start Project on http://127.0.0.1:8000 (requires Symfony CLI)
	symfony server:start

.PHONY: stop
stop: ## stop -- Stop the server (requires Symfony CLI)
	symfony server:stop

.PHONY: db
db: ## db -- Creates the database
	php bin/console d:d:c

.PHONY: migrations
migrations: ## migrations -- Runs the migrations with no interaction
	php bin/console d:m:m --no-interaction

.PHONY: fulldb
fulldb: ## fulldb -- Creates the database and runs the migrations with no interaction
	php bin/console d:d:c && php bin/console d:m:m --no-interaction

.PHONY: deletedb
deletedb: ## deletedb -- Deletes the database
	php bin/console d:d:d --force

.PHONY: fixtures
fixtures: ## fixtures -- Runs fixtures with no interaction
	php bin/console d:f:l --no-interaction
