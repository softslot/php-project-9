setup:
	composer install
	cp -n .env.example .env
	php artisan key:gen --ansi
	touch database/database.sqlite
	php artisan migrate
	php artisan db:seed

start:
	php artisan serve --host 0.0.0.0

validate:
	composer validate

du:
	composer dump-autoload

lint:
	composer exec --verbose phpcs -- --standard=PSR12 app

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 app

test:
	composer exec --verbose phpunit tests

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover ./build/logs/clover.xml