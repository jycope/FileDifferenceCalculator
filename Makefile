install:
		composer install

autoload:
		composer dump-autoload

test:
		composer run-script phpunit -- --colors=always tests

lint:
		composer exec --verbose phpcs -- --standard=PSR12 src bin tests

test-coverage:
		composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

validate:
		composer validate

clearcache:
		composer clearcache