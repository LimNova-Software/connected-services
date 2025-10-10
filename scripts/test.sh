#!/bin/bash

set -e

echo "Running PHPUnit tests..."
./vendor/bin/phpunit --configuration phpunit.xml

echo "Running PHPStan analysis..."
./vendor/bin/phpstan analyse --configuration phpstan.neon

echo "Running Laravel Pint formatting check..."
./vendor/bin/pint --test

echo "All checks passed!"
