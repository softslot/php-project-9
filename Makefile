setup:
	composer install
	cp -n .env.example .env
	php artisan key:gen --ansi
	touch database/database.sqlite
	php artisan migrate:refresh
	php artisan db:seed
	npm ci
	npm run build

deploy:
	cp -n .env.example .env
	php artisan key:gen --ansi
	php artisan migrate --force
	npm run build

PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

validate:
	composer validate

du:
	composer dump-autoload

lint:
	composer exec --verbose phpcs -- --standard=PSR12 app routes tests

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 app routes tests

test:
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage php artisan test --coverage-clover build/logs/clover.xml
