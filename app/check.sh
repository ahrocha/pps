#!/bin/bash

echo -e "\n PHPStan"
vendor/bin/phpstan analyse src --level=max

echo -e "\n PHPCS"
vendor/bin/phpcs --standard=PSR12 src

echo -e "\n🧪PHPUnit"
vendor/bin/phpunit --testdox

echo -e "\n Ok!"
