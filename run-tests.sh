#!/bin/bash

# Simple test runner script
echo "Running ShortKenny Test Suite..."
echo ""

# Check if composer is installed
if [ ! -f "vendor/bin/phpunit" ]; then
    echo "Installing dependencies..."
    composer install
fi

# Run tests
echo "Running PHPUnit tests..."
./vendor/bin/phpunit

# Check exit code
if [ $? -eq 0 ]; then
    echo ""
    echo "✅ All tests passed!"
else
    echo ""
    echo "❌ Some tests failed!"
    exit 1
fi

