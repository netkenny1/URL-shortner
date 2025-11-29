# ShortKenny URL Shortener - Assignment Report

**Author:** Kenny Tohme  
**Course:** Software Development and DevOps (SDDO)  
**Assignment:** Individual Assignment 2  
**Date:** January 2025

---

## Executive Summary

This report documents the comprehensive improvements made to the ShortKenny URL shortener application to meet the requirements of Individual Assignment 2. The project has been transformed from a basic functional application into a production-ready system with automated testing, CI/CD pipelines, containerization, and monitoring capabilities. All requirements have been successfully implemented, including code quality improvements, 70%+ test coverage, automated CI/CD workflows, Docker containerization, and observability features.

---

## 1. Code Quality Improvements

### 1.1 Refactoring and SOLID Principles

The original codebase was refactored to follow SOLID principles and eliminate common code smells:

#### **Single Responsibility Principle (SRP)**
- **Before:** Business logic, data access, and validation were mixed in single files
- **After:** Separated into distinct classes:
  - `LinkRepository` - Handles all database operations
  - `LinkService` - Contains business logic
  - `UrlValidator` - Validates URL format
  - `ShortCodeGenerator` - Generates unique short codes
  - `ResponseHelper` - Manages HTTP responses
  - `HealthChecker` - Performs health checks
  - `MetricsCollector` - Collects and aggregates metrics

#### **Open/Closed Principle**
- Classes are designed to be extended without modification
- New features can be added through composition rather than changing existing code
- Service layer allows for easy addition of new business rules

#### **Dependency Inversion**
- High-level modules (Service) depend on abstractions (Repository interface)
- Dependency injection is used throughout
- Database connection is abstracted, allowing easy switching between SQLite and PostgreSQL

### 1.2 Elimination of Code Smells

#### **Magic Numbers Removed**
- **Before:** Hardcoded values like `6` for code length, `50` for limits
- **After:** All constants moved to `Constants.php`:
  ```php
  const SHORT_CODE_LENGTH = 6;
  const DEFAULT_LIMIT = 50;
  const HTTP_OK = 200;
  ```

#### **Code Duplication Eliminated**
- **Before:** Database queries repeated across multiple files
- **After:** Centralized in `LinkRepository` with reusable methods
- **Before:** URL validation logic duplicated
- **After:** Single `UrlValidator` class used throughout

#### **Long Functions Refactored**
- **Before:** `api.php` contained 60+ lines of mixed concerns
- **After:** Split into service calls with clear separation of concerns
- Each function now has a single, clear purpose

#### **Hardcoding Removed**
- Database DSN configurable via environment variables
- HTTP status codes use constants
- All configuration values externalized

### 1.3 Improved Structure and Modularity

The new architecture follows a layered approach:

```
┌─────────────────┐
│   Router/API   │  (Entry points)
└────────┬────────┘
         │
┌────────▼────────┐
│  LinkService    │  (Business Logic)
└────────┬────────┘
         │
┌────────▼────────┐
│ LinkRepository  │  (Data Access)
└────────┬────────┘
         │
┌────────▼────────┐
│   Database      │  (SQLite/PostgreSQL)
└─────────────────┘
```

This structure provides:
- **Testability:** Each layer can be tested independently
- **Maintainability:** Changes are isolated to specific layers
- **Scalability:** Easy to add new features or swap implementations

---

## 2. Testing and Coverage Implementation

### 2.1 Test Strategy

A comprehensive test suite was implemented using PHPUnit 10, covering both unit and integration tests:

#### **Unit Tests**
Located in `tests/Unit/`, these test individual components in isolation:

- **UrlValidatorTest:** Tests URL validation logic
  - Valid HTTP/HTTPS URLs
  - Invalid URLs (missing protocol, empty strings)
  - Exception handling

- **ShortCodeGeneratorTest:** Tests code generation
  - Correct length generation
  - Alphanumeric character validation
  - Uniqueness (statistical)

- **MetricsCollectorTest:** Tests metrics collection
  - Request counting
  - Error tracking
  - Latency calculation
  - Prometheus format output

- **ResponseHelperTest:** Tests HTTP response helpers
  - JSON encoding
  - Status code setting
  - Error response formatting

#### **Integration Tests**
Located in `tests/Integration/`, these test component interactions:

- **LinkRepositoryTest:** Tests database operations
  - CRUD operations
  - Short code uniqueness
  - Click count increments
  - Query operations

- **LinkServiceTest:** Tests business logic
  - Link creation with validation
  - Update operations
  - Delete operations
  - Redirect functionality

- **HealthCheckerTest:** Tests health check functionality
  - Database connectivity
  - Schema validation
  - Status reporting

### 2.2 Coverage Implementation

#### **Coverage Configuration**
PHPUnit is configured in `phpunit.xml` with:
- HTML coverage reports in `coverage/html/`
- Clover XML for CI integration in `coverage/clover.xml`
- Text reports in `coverage/coverage.txt`

#### **Coverage Targets**
- **Minimum Threshold:** 70% (enforced in CI pipeline)
- **Current Coverage:** All critical paths covered including:
  - All service methods
  - All repository methods
  - Validation logic
  - Error handling
  - Edge cases

#### **Coverage Measurement**
Coverage is measured automatically in the CI pipeline:
1. Tests run with Xdebug coverage
2. Coverage data collected in Clover XML format
3. Coverage percentage calculated
4. Pipeline fails if below 70%

### 2.3 Test Execution

Tests can be run:
- **Locally:** `./vendor/bin/phpunit`
- **In CI:** Automatically on every push/PR
- **With Coverage:** `./vendor/bin/phpunit --coverage-html coverage/html`

---

## 3. CI/CD Architecture and Execution

### 3.1 Continuous Integration Pipeline

The CI pipeline (`.github/workflows/ci.yml`) implements a comprehensive testing and validation workflow:

#### **Pipeline Stages**

1. **Code Checkout**
   - Uses `actions/checkout@v4` to fetch repository code

2. **PHP Environment Setup**
   - Uses `shivammathur/setup-php@v2`
   - PHP 8.2 with SQLite extensions
   - Xdebug enabled for coverage

3. **Dependency Installation**
   - Installs Composer dependencies
   - Prepares test environment

4. **Syntax Validation**
   - Validates PHP syntax for all files
   - Catches syntax errors before testing

5. **Test Execution**
   - Runs full test suite
   - Generates coverage reports in multiple formats
   - Ensures all tests pass

6. **Coverage Verification**
   - Parses Clover XML to calculate coverage percentage
   - Compares against 70% threshold
   - **Fails pipeline if coverage is insufficient**

7. **Docker Build Test**
   - Builds Docker image
   - Runs container
   - Tests health endpoint
   - Validates containerization

8. **Artifact Storage**
   - Uploads coverage reports as artifacts
   - Retains for 30 days
   - Accessible from GitHub Actions UI

#### **Pipeline Triggers**
- Push to `main` or `develop` branches
- Pull requests to `main` or `develop`
- Manual workflow dispatch (optional)

### 3.2 Continuous Deployment Pipeline

The CD pipeline (`.github/workflows/deploy.yml`) handles automated deployment:

#### **Deployment Stages**

1. **Code Checkout**
   - Fetches latest code from `main` branch

2. **Docker Buildx Setup**
   - Prepares Docker build environment
   - Enables multi-platform builds (if needed)

3. **Docker Authentication** (Optional)
   - Logs into Docker Hub if credentials provided
   - Enables image pushing to registry

4. **Image Building**
   - Builds Docker image
   - Tags with commit SHA for traceability
   - Tags as `latest` for convenience

5. **Image Storage**
   - Saves image as compressed archive
   - Uploads as GitHub artifact
   - Retains for 7 days

6. **Deployment Execution** (Configurable)
   - Deploys to production server
   - Uses SSH with key-based authentication
   - Loads image and restarts services
   - Can be enabled/disabled via secrets

#### **Security Considerations**
- All sensitive data stored as GitHub Secrets
- No credentials in repository
- SSH key-based authentication
- Environment-specific configuration

#### **Deployment Configuration**
Required GitHub Secrets:
- `DOCKER_USERNAME` / `DOCKER_PASSWORD` (optional, for registry)
- `DEPLOY_ENABLED` (set to `true` to enable)
- `DEPLOY_HOST` (production server)
- `DEPLOY_USER` (SSH user)
- `DEPLOY_KEY` (SSH private key)

### 3.3 Pipeline Execution Flow

```
┌─────────────┐
│   Push/PR   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  CI Pipeline│
│  (Always)   │
└──────┬──────┘
       │
       ├──► Tests
       ├──► Coverage Check
       ├──► Docker Build
       └──► Artifact Upload
       
       │
       ▼ (if main branch)
┌─────────────┐
│  CD Pipeline │
│  (Main only)│
└──────┬──────┘
       │
       ├──► Build Image
       ├──► Tag Image
       └──► Deploy (if enabled)
```

---

## 4. Deployment and Containerization

### 4.1 Docker Containerization

#### **Dockerfile Improvements**

The Dockerfile was enhanced for production readiness:

**Base Image:**
- PHP 8.2 with Apache
- Optimized for web applications

**Dependencies:**
- SQLite3 libraries
- PDO extensions
- Composer for dependency management

**Optimizations:**
- Multi-stage build considerations
- Layer caching optimization
- Minimal image size
- Security hardening

**Health Checks:**
- Built-in health check endpoint
- Automatic container restart on failure
- Health status monitoring

#### **Docker Compose Setup**

The `docker-compose.yml` file provides a complete development and monitoring stack:

**Services:**

1. **Application Service (`app`)**
   - Builds from Dockerfile
   - Exposes port 8080
   - Volume mounts for data persistence
   - Environment variable configuration
   - Health check configuration

2. **Prometheus Service**
   - Official Prometheus image
   - Scrapes metrics from application
   - Persistent volume for metrics storage
   - Configuration via `prometheus.yml`

3. **Grafana Service**
   - Official Grafana image
   - Pre-configured admin credentials
   - Persistent volume for dashboards
   - Auto-connects to Prometheus

**Volume Management:**
- Persistent volumes for data retention
- Separate volumes for each service
- Easy backup and restore

### 4.2 Deployment Strategy

#### **Local Development**
```bash
docker-compose up -d
```
- All services start together
- Automatic service discovery
- Easy debugging and development

#### **Production Deployment**

**Option 1: Docker Compose (Recommended for small deployments)**
```bash
docker-compose -f docker-compose.prod.yml up -d
```

**Option 2: Kubernetes (For scalable deployments)**
- Docker image can be deployed to Kubernetes
- Health checks enable automatic recovery
- Horizontal scaling supported

**Option 3: Cloud Platforms**
- Compatible with AWS ECS, Google Cloud Run, Azure Container Instances
- Environment variables for configuration
- Health endpoints for load balancer integration

### 4.3 Environment Configuration

The application supports environment-based configuration:

**Database Configuration:**
- SQLite for local development (default)
- PostgreSQL for production (via `DB_DSN` environment variable)
- Easy switching without code changes

**Deployment Variables:**
- All sensitive data via environment variables
- No hardcoded credentials
- Secure secret management

---

## 5. Monitoring Configuration

### 5.1 Health Endpoint

The `/health` endpoint provides comprehensive service status:

**Implementation:**
- `HealthChecker` class performs system checks
- Database connectivity verification
- Schema validation
- Returns structured JSON response

**Response Format:**
```json
{
  "status": "healthy|unhealthy",
  "timestamp": "ISO 8601 timestamp",
  "checks": {
    "database": "ok|error message",
    "schema": "ok|error message"
  }
}
```

**Usage:**
- Load balancer health checks
- Kubernetes liveness/readiness probes
- Monitoring system integration
- Manual service verification

### 5.2 Metrics Collection

#### **MetricsCollector Implementation**

The `MetricsCollector` class provides comprehensive metrics:

**Collected Metrics:**
- **Request Count:** Total HTTP requests
- **Error Count:** Total errors (4xx, 5xx)
- **Success Count:** Successful requests (2xx, 3xx)
- **Average Latency:** Mean response time in milliseconds
- **P95 Latency:** 95th percentile response time
- **P99 Latency:** 99th percentile response time

**Data Structure:**
- In-memory storage for performance
- Rolling window (last 1000 requests)
- Thread-safe operations
- Minimal performance impact

#### **Prometheus Integration**

**Metrics Endpoint (`/metrics`):**
- Returns Prometheus-compatible format
- Standard metric types (counter, gauge)
- Proper labeling and formatting

**Example Output:**
```
# HELP http_requests_total Total number of HTTP requests
# TYPE http_requests_total counter
http_requests_total 1234

# HELP http_errors_total Total number of HTTP errors
# TYPE http_errors_total counter
http_errors_total 5

# HELP http_request_duration_ms Average request latency
# TYPE http_request_duration_ms gauge
http_request_duration_ms 45.23
```

### 5.3 Prometheus Configuration

**Configuration File (`prometheus/prometheus.yml`):**
```yaml
global:
  scrape_interval: 15s
  evaluation_interval: 15s

scrape_configs:
  - job_name: 'shortkenny'
    static_configs:
      - targets: ['app:80']
    metrics_path: '/metrics'
    scrape_interval: 10s
```

**Features:**
- Automatic metric scraping
- Configurable intervals
- Service discovery ready
- Alert rule support (extensible)

### 5.4 Grafana Setup

**Pre-configured:**
- Prometheus as default data source
- Persistent storage for dashboards
- Admin credentials (change in production)

**Dashboard Creation:**
1. Login to Grafana (http://localhost:3000)
2. Add Prometheus data source: `http://prometheus:9090`
3. Create dashboards with:
   - Request rate graphs
   - Error rate visualization
   - Latency percentiles
   - Health status indicators

**Recommended Dashboards:**
- **Overview:** Request count, error rate, latency
- **Performance:** P95/P99 latency trends
- **Health:** Service status over time
- **Errors:** Error breakdown by type

### 5.5 Observability Benefits

**Operational Insights:**
- Real-time service health monitoring
- Performance trend analysis
- Error detection and alerting
- Capacity planning data

**Troubleshooting:**
- Latency spikes identification
- Error pattern analysis
- Request volume correlation
- Service dependency health

---

## 6. Conclusion

### 6.1 Achievements

All assignment requirements have been successfully implemented:

✅ **Code Quality:**
- SOLID principles applied throughout
- Code duplication eliminated
- Magic numbers removed
- Modular, testable structure

✅ **Testing:**
- Comprehensive unit and integration tests
- 70%+ code coverage achieved
- Automated coverage verification
- Test reports stored in repository

✅ **CI/CD:**
- Automated CI pipeline with GitHub Actions
- Coverage threshold enforcement
- Docker build and test
- CD pipeline for main branch
- Secure secret management

✅ **Containerization:**
- Production-ready Dockerfile
- Docker Compose for local development
- Health checks and monitoring
- Environment configuration

✅ **Monitoring:**
- Health endpoint implementation
- Metrics collection (request count, latency, errors)
- Prometheus integration
- Grafana setup and configuration

✅ **Documentation:**
- Comprehensive README.md
- Detailed REPORT.md (this document)
- Code comments and inline documentation

### 6.2 Key Improvements Summary

1. **Architecture:** Transformed from monolithic to layered architecture
2. **Testability:** Increased from basic tests to comprehensive suite
3. **Automation:** Added full CI/CD pipelines
4. **Observability:** Implemented health checks and metrics
5. **Production Readiness:** Containerized and deployable

### 6.3 Future Enhancements

Potential improvements for production use:
- Rate limiting for API endpoints
- Authentication and authorization
- Link expiration and cleanup
- Custom short code support
- Analytics dashboard
- Multi-database support (PostgreSQL, MySQL)
- Caching layer (Redis)
- Load balancing configuration
- SSL/TLS termination
- Backup and recovery procedures

---

## References

- PHPUnit Documentation: https://phpunit.de/
- Docker Documentation: https://docs.docker.com/
- Prometheus Documentation: https://prometheus.io/docs/
- Grafana Documentation: https://grafana.com/docs/
- GitHub Actions Documentation: https://docs.github.com/en/actions
- SOLID Principles: https://en.wikipedia.org/wiki/SOLID

---

**End of Report**
