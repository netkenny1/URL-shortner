# Quick Start Guide

## Prerequisites Check

```bash
# Check PHP version (8.0+ required)
php -v

# Check if Composer is installed
composer --version

# Check if Docker is installed (optional)
docker --version
docker-compose --version
```

## Initial Setup

```bash
# 1. Install dependencies
composer install

# 2. Start the application
php -S 127.0.0.1:8000 router.php

# 3. Open in browser
# http://127.0.0.1:8000
```

## Running Tests

```bash
# Option 1: Using Composer
composer test

# Option 2: Using PHPUnit directly
./vendor/bin/phpunit

# Option 3: Using the test script
./run-tests.sh
```

## Docker Setup

```bash
# Start all services (app + monitoring)
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down
```

## Access Points

- **Application:** http://localhost:8080
- **Health Check:** http://localhost:8080/health
- **Metrics:** http://localhost:8080/metrics
- **Prometheus:** http://localhost:9090
- **Grafana:** http://localhost:3000 (admin/admin)

## Common Commands

```bash
# Run tests with coverage
composer test-coverage

# View coverage report
open coverage/html/index.html

# Rebuild Docker image
docker-compose build --no-cache

# Check application health
curl http://localhost:8080/health

# View metrics
curl http://localhost:8080/metrics
```

