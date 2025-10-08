#!/bin/bash

set -e

echo "Running Laravel Pint formatting..."
./vendor/bin/pint

echo "Running Rector refactoring..."
./vendor/bin/rector process --dry-run

echo "Formatting complete!"
