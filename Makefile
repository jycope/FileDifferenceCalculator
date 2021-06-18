install:
		composer install

autoload:
		composer dump-autoload

test:
		composer run-script phpunit -- --colors=always tests --coverage-text coverage

lint:
		composer exec --verbose phpcs -- --standard=PSR12 src bin tests


test-coverage:
		composer exec --verbose phpunit -- --colors=always tests -- --coverage-text --coverage-html coverage

validate:
		composer validate

clearcache:
		composer clearcache