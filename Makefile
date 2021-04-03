PHP_BIN=php

all:
	$(PHP_BIN) vendor/bin/phpunit --configuration=phpunit.xml --stop-on-failure --testdox
