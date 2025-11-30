# ShortKenny - URL Shortener

## 📌 Project Overview

**ShortKenny** is a production-ready URL shortener application built with PHP, featuring comprehensive testing, CI/CD automation, containerization, and monitoring capabilities. This project was developed as part of **Individual Assignment 2 for SDDO**.

### Core Features
- 🔗 **URL Shortening:** Convert long URLs into short, shareable codes
- 🔄 **Redirection:** Automatic redirect from short codes to original URLs
- 📊 **Click Tracking:** Track usage statistics for each shortened link
- 📜 **RESTful API:** Complete API for creating, reading, updating, and deleting links
- 💾 **Persistent Storage:** SQLite database for data persistence
- 🏥 **Health Monitoring:** `/health` endpoint for service status
- 📈 **Metrics:** Prometheus-compatible metrics endpoint
- 🐳 **Containerized:** Full Docker support with docker-compose
- ✅ **Tested:** Comprehensive test suite with 70%+ code coverage
- 🔄 **CI/CD:** Automated testing and deployment pipelines

---

## 🛠️ Tech Stack

| Layer         | Technology Used                    |
|---------------|-----------------------------------|
| **Backend**   | PHP 8.2+                          |
| **Frontend**  | HTML, CSS, JavaScript             |
| **Database**  | SQLite (with PostgreSQL support)  |
| **Testing**   | PHPUnit 10                        |
| **Container** | Docker & Docker Compose           |
| **CI/CD**     | GitHub Actions                    |
| **Monitoring**| Prometheus & Grafana              |

---

## 📁 Project Structure

```
.
├── src/                    # Refactored application classes
│   ├── Constants.php       # Application constants
│   ├── UrlValidator.php    # URL validation logic
│   ├── ShortCodeGenerator.php  # Short code generation
│   ├── LinkRepository.php  # Data access layer
│   ├── LinkService.php     # Business logic layer
│   ├── MetricsCollector.php    # Metrics collection
│   ├── ResponseHelper.php  # HTTP response helpers
│   ├── HealthChecker.php   # Health check logic
│   └── autoload.php        # Class autoloader
├── db/
│   └── config.php          # Database configuration
├── tests/                  # Test suite
│   ├── Unit/               # Unit tests
│   ├── Integration/        # Integration tests
│   └── bootstrap.php       # Test bootstrap
├── prometheus/
│   └── prometheus.yml      # Prometheus configuration
├── .github/
│   └── workflows/          # CI/CD pipelines
│       ├── ci.yml          # Continuous Integration
│       └── deploy.yml      # Continuous Deployment
├── api.php                 # API endpoints
├── redirect.php            # Redirect handler
├── router.php              # Request router
├── index.php               # Frontend UI
├── lib.php                 # Legacy compatibility
├── Dockerfile              # Docker image definition
├── docker-compose.yml      # Multi-container setup
├── composer.json           # PHP dependencies
├── phpunit.xml             # PHPUnit configuration
└── README.md               # This file
```

---

## 🚀 Installation

### Prerequisites

- **PHP** 8.0 or higher
- **Composer** (for dependency management)
- **Docker** and **Docker Compose** (optional, for containerized deployment)

### Install Dependencies

```bash
# Install PHP dependencies
composer install
```

---

## 🏃 Running Locally

### Option 1: PHP Built-in Server

1. Start the development server:
   ```bash
   php -S 127.0.0.1:8000 router.php
   ```

2. Open your browser:
   ```
   http://127.0.0.1:8000
   ```

### Option 2: Docker Compose (Recommended)

1. Build and start all services:
   ```bash
   docker-compose up --build -d
   ```

2. Access the application:
   - **Application:** http://localhost:8080
   - **Prometheus:** http://localhost:9090
   - **Grafana:** http://localhost:3000 (admin/admin)

3. Stop services:
   ```bash
   docker-compose down
   ```

### Option 3: Docker Only

1. Build the image:
   ```bash
   docker build -t shortkenny .
   ```

2. Run the container:
   ```bash
   docker run -d -p 8080:80 --name shortkenny shortkenny
   ```

---

## 🧪 Running Tests

### Run All Tests (Single Command)

You can run all tests with a single command:

```bash
# Option 1: Using Composer (recommended)
composer test

# Option 2: Using PHPUnit directly
./vendor/bin/phpunit

# Option 3: Using the test runner script
./run-tests.sh
```

All three commands will:
- ✅ Run all unit and integration tests
- ✅ Validate link shortening functionality
- ✅ Validate redirection functionality
- ✅ Test service logic locally
- ✅ Display test results and coverage summary

### Run Specific Test Suites

```bash
# Unit tests only
./vendor/bin/phpunit tests/Unit

# Integration tests only
./vendor/bin/phpunit tests/Integration
```

### View Code Coverage

```bash
# Generate coverage report
./vendor/bin/phpunit --coverage-html coverage/html

# View in browser
open coverage/html/index.html
```

Coverage reports are also available in:
- `coverage/html/` - HTML report
- `coverage/clover.xml` - Clover XML format
- `coverage/coverage.txt` - Text format

**Coverage Target:** Minimum 70% (enforced in CI pipeline)

---

## 📊 Monitoring & Observability

### Health Endpoint

Check service health:
```bash
curl http://localhost:8080/health
```

Response:
```json
{
  "status": "healthy",
  "timestamp": "2025-01-15T10:30:00+00:00",
  "checks": {
    "database": "ok",
    "schema": "ok"
  }
}
```

### Metrics Endpoint

Get Prometheus-compatible metrics:
```bash
curl http://localhost:8080/metrics
```

Available metrics:
- `http_requests_total` - Total HTTP requests
- `http_errors_total` - Total HTTP errors
- `http_request_duration_ms` - Average request latency
- `http_request_duration_p95_ms` - 95th percentile latency
- `http_request_duration_p99_ms` - 99th percentile latency

### Prometheus & Grafana Setup

1. Start services with docker-compose (includes Prometheus and Grafana)

2. Configure Grafana:
   - Login at http://localhost:3000 (admin/admin)
   - Add Prometheus as data source: `http://prometheus:9090`
   - Import dashboards or create custom ones

3. Prometheus configuration is in `prometheus/prometheus.yml`

---

## 🚀 Deployment

### Google Cloud Platform Deployment

This application is deployed to **Google Cloud Run** using Docker.

**Live Application:**
- **URL:** https://shortkenny-644197836082.europe-west1.run.app
- **Project ID:** `deep-ray-479811-a3`
- **Region:** `europe-west1`

The application is automatically deployed via GitHub Actions when code is pushed to the `main` branch.

## 🔄 CI/CD Pipeline

### Continuous Integration (CI)

The CI pipeline (`.github/workflows/ci.yml`) runs on every push and pull request:

1. **Checkout code**
2. **Setup PHP environment**
3. **Install dependencies**
4. **PHP syntax check**
5. **Run tests with coverage**
6. **Verify coverage threshold (70% minimum)**
7. **Build Docker image**
8. **Test Docker image**

### Continuous Deployment (CD)

The CD pipeline (`.github/workflows/deploy.yml`) runs only on pushes to `main` branch:

1. **Checkout code**
2. **Set up Google Cloud SDK**
3. **Build Docker image**
4. **Push image to Google Container Registry (GCR)**
5. **Deploy to Google Cloud Run**

### Google Cloud Configuration

The application is deployed to Google Cloud Run with the following configuration:
- **Project ID:** `deep-ray-479811-a3`
- **Region:** `europe-west1`
- **Service Name:** `shortkenny`
- **Platform:** Cloud Run (managed)

For professor access to view Cloud Run console, logs, and metrics, see `PROFESSOR_ACCESS_GUIDE.md`.

---

## 📡 API Endpoints

### Create Short Link

```bash
POST /api/links
Content-Type: application/json

{
  "original_url": "https://example.com/very/long/url"
}
```

Response:
```json
{
  "id": 1,
  "original_url": "https://example.com/very/long/url",
  "short_code": "abc123",
  "click_count": 0,
  "created_at": "2025-01-15 10:30:00",
  "updated_at": "2025-01-15 10:30:00"
}
```

### List All Links

```bash
GET /api/links
```

### Get Link by ID

```bash
GET /api/links/{id}
```

### Update Link

```bash
PUT /api/links/{id}
Content-Type: application/json

{
  "original_url": "https://newexample.com"
}
```

### Delete Link

```bash
DELETE /api/links/{id}
```

### Redirect

```bash
GET /{short_code}
# Returns 302 redirect to original URL
```

---

## 🏗️ Architecture

### Design Principles

The application follows **SOLID principles**:

- **Single Responsibility:** Each class has one clear purpose
- **Open/Closed:** Extensible without modification
- **Liskov Substitution:** Proper inheritance and interfaces
- **Interface Segregation:** Focused, minimal interfaces
- **Dependency Inversion:** Depend on abstractions

### Code Quality Improvements

- ✅ Removed code duplication
- ✅ Eliminated magic numbers (moved to Constants)
- ✅ Removed hardcoded values
- ✅ Modular, testable structure
- ✅ Separation of concerns (Repository, Service, Controller layers)
- ✅ Comprehensive error handling
- ✅ Type safety and validation

---

## 🔒 Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_DSN` | Database connection string | `sqlite:./data.sqlite` |

Example for PostgreSQL:
```bash
export DB_DSN="pgsql:host=localhost;dbname=shortkenny;user=user;password=pass"
```

---

## 📝 Development

### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions small and focused

### Adding New Features

1. Create feature branch
2. Write tests first (TDD approach)
3. Implement feature
4. Ensure tests pass and coverage is maintained
5. Submit pull request

---

## 🐛 Troubleshooting

### Database Issues

If the database file is locked or corrupted:
```bash
# Remove and recreate
rm data.sqlite
# Restart application - schema will be created automatically
```

### Docker Issues

```bash
# Clean rebuild
docker-compose down -v
docker-compose up --build
```

### Test Failures

```bash
# Clear cache and rerun
rm -rf vendor/
composer install
./vendor/bin/phpunit
```

---

## 📄 License

This project is developed for educational purposes as part of SDDO course at IE University.

---

## 👤 Author

**Kenny Tohme**  
SDDO - Individual Assignment 2  
IE University

---

## 📚 Additional Documentation

See `REPORT.md` for detailed information about:
- Code improvements and refactoring
- Testing strategy and coverage implementation
- CI/CD architecture and execution
- Deployment and containerization
- Monitoring configuration
