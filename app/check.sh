#!/bin/bash

# echo -e "\n PHPStan"
vendor/bin/phpstan analyse src --level=5

echo -e "\n PHPCS"
vendor/bin/php-cs-fixer fix src
vendor/bin/phpcbf --standard=PSR12 src
vendor/bin/phpcs --standard=PSR12 src

echo -e "\n PHPUnit"
vendor/bin/phpunit --testdox

echo -e "\n Ok!"
