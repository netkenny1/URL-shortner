Title: ShortKenny URL Shortener
Author: Kenny Tohme
Date: 4 October 2025

## Abstract
A compact URL shortener built with PHP and SQLite, designed to support a browser UI and a small REST style API. Core behavior: accept a long URL, create a short code, store it, and on requests to /{code} redirect to the original URL while incrementing a click counter. The codebase stays minimal and readable to make CI, containerization, and later DevOps work straightforward. This document covers the development approach and SDLC, requirements, architecture and data model, implementation details, testing, the minor challenges faced, and a practical plan to scale for DevOps.

---

## 1. What the app does
The application provides a simple page to paste a long URL and receive a short link at the root of the site. Visiting that short path resolves the original URL from SQLite, increases a stored click count, and returns an HTTP 302 redirect. A small API exposes the same actions for HTTP clients. Data persists in a single SQLite file committed with the repository. Local run uses PHP’s built in server with a front controller.

**Features implemented**
- Create short links from valid long URLs
- Redirect from short code to original URL
- Increment click count on each redirect
- List stored links and counts via API or UI
- Minimal browser UI that calls the API

---

## 2. SDLC model and rationale
**Model:** Iterative and incremental.

**Why this fit:** Scope is small and the features are separable. Short cycles allowed getting a working base early, validating the redirect in a real browser, then layering the API and UI, followed by persistence and small polish items. This rhythm aligns with DevOps habits like frequent integration, fast checks, and small changes.

**Phases completed up to development**
- **Planning:** Set goal to ship a functional shortener that runs with one command, uses zero external services, and can act as a clean seed for a DevOps pipeline.
- **Requirements:** Defined a tight set of functional features plus simple nonfunctional constraints around readability and setup.
- **Design:** Chose a front controller plus two handlers (API and redirect), with SQLite behind a small helper library. One table schema for links.
- **Development:** Implemented the DB helpers, API endpoints, redirect flow, and minimal UI in short iterations.

---

## 3. Requirements

### Functional
- Create a short link from a long URL
- Redirect visitors from `/{code}` to the original URL
- Increment and store a click counter for each redirect
- Provide a way to list links with their counts

### Nonfunctional
- Runs locally without extra services beyond PHP
- Persistent storage with a simple SQL database
- Clear repository layout and readable code
- Minimal UI that exercises the same API used by clients

---

## 4. Architecture overview

**High level structure**
- **router.php**: Front controller. Parses the request path and dispatches.
- **api.php**: REST style endpoints for creating and listing links. Returns JSON and status codes.
- **redirect.php**: Looks up the short code, increments clicks, issues a 302 redirect to the long URL.
- **lib.php**: Database connection (PDO SQLite), table migration, validation, code generation, and CRUD style helpers.
- **SQLite**: One table storing long_url, code, clicks, timestamps.

**Data model**
- **Table: links**
  - id INTEGER PRIMARY KEY AUTOINCREMENT
  - long_url TEXT NOT NULL
  - code TEXT NOT NULL UNIQUE
  - clicks INTEGER NOT NULL DEFAULT 0
  - created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- Index on `code` supports fast lookups during redirect.

**Flow summary**
1. Browser or HTTP client sends a request.
2. `router.php` routes either to the API or redirect handler.
3. Handlers call `lib.php` for database operations.
4. SQLite persists and retrieves link data.

---

## 5. Implementation details

### Database and helpers (lib.php)
- **Connection:** Single PDO connection to a local SQLite file with exceptions enabled.
- **Migration:** On first run, ensures the `links` table and index exist.
- **Validation:** Long URLs validated with PHP’s filter utilities to reject obvious bad input.
- **Code generation:** Alphanumeric short code creator. Insert guarded by a UNIQUE constraint; on conflict, a new code is generated and retried.
- **Helpers exposed:** create short link, fetch by code, increment clicks, list recent links.

### Routing (router.php)
- All requests enter through one script.
- `/` serves the minimal UI.
- `/api/...` goes to `api.php`.
- Any single path segment not under `/api` is treated as a candidate short code and sent to `redirect.php`.

### API (api.php)
- **POST /api/shorten:** Accepts JSON with `url`, validates, stores, and returns `{ code, long_url, short_url }`.
- **GET /api/links:** Returns recent links with `code`, `long_url`, `clicks`, and `created_at`.
- Uses consistent JSON errors and appropriate HTTP status codes.

### Redirect handler (redirect.php)
- Extracts `{code}` from path.
- Retrieves the row by code; if found, increments `clicks` and issues `302 Location: {long_url}`.
- Returns 404 for unknown codes with a clear message.

### User interface (index.php)
- Plain HTML with a small script.
- Input field for long URL, button to shorten, live result showing the generated short URL.
- Fetches and displays recent links and counters.

### Local run
- Start server with:
  - `php -S 127.0.0.1:8000 router.php`
- Open browser at `http://127.0.0.1:8000`
- Use the page or make API calls from any HTTP client.

---

## 6. Testing

### Manual checks
- **UI path:** Paste a valid URL, create a short link, click it, confirm redirect and increasing click count in the list.
- **API path:** 
  - `POST /api/shorten` with JSON body to receive code and short_url.
  - `GET /api/links` to confirm the new record and current `clicks`.

### Negative tests
- Submit invalid strings as URLs to confirm the API rejects them with a clean error response.
- Request an unknown short code and confirm a 404 response.

### Quick code sanity
- Run PHP syntax checks across `.php` files.
- Apply the schema against an in memory SQLite database in a one line script to validate SQL.

---

## 7. Challenges and how they were handled

**Short code collisions**
Keeping the generator simple while avoiding duplicates was the goal. A UNIQUE constraint on `code` lets SQLite enforce uniqueness. On the rare collision, the insert fails and a new code is generated and tried again. Small, reliable, and no extra dependencies.

**URL validation**
Only real URLs should enter the database. PHP’s built in filter validation does the job. Invalid inputs return a clear JSON error from the API, and the UI surfaces that message without duplicating rules.

**Routing without a framework**
A full framework felt unnecessary for a teaching project. A single front controller with a couple of path checks keeps the redirect path fast and the API logic easy to follow.

**Zero config persistence**
SQLite keeps local setup frictionless. No external services, no credentials, and SQL stays available for later migration to a larger database.

**Tiny UI**
Avoided build tools and heavy client frameworks. A plain page with `fetch` calls was enough to exercise the API and show results.

---

## 8. DevOps-oriented scaling plan

**Continuous Integration**
- Add a GitHub Actions workflow that:
  - Runs `php -l` on all PHP files.
  - Executes a short PHP snippet that applies the schema to an in memory SQLite database.
  - Later, include unit tests for helpers like URL validation and code generation.

**Containerization**
- Build an image from `php:8.x-apache`.
- Copy the repo into the web root and enable rewrite so requests reach `router.php`.
- For local persistence, mount a volume for the SQLite file.

**Configuration and environments**
- Introduce an environment variable for the database DSN.
- Keep SQLite for local development.
- Support Postgres for staging or production by switching the DSN in one place inside `lib.php`.

**Observability**
- Log redirect events with timestamp and code.
- Expose a lightweight stats endpoint or page to visualize clicks over time.

**Security and hygiene**
- Add rate limiting on `POST /api/shorten`.
- Optional admin area or token to allow custom aliases and protected operations.
- Link expiry and soft delete for cleanup.

**Performance**
- Ensure indexes on `code` and `created_at` are present.
- If traffic grows, consider a read-through cache for code lookups.
- Moving from SQLite to Postgres removes the single-writer limitation.

**Team workflow**
- Protect the main branch with required CI checks.
- Keep pull requests small and messages descriptive.

---

## 9. Conclusion
The URL shortener satisfies the assignment’s feature set with a small, clear architecture and a minimal UI. Storage is persistent, the API is simple to use, and local setup is frictionless. The codebase is intentionally straightforward so it can serve as a clean starting point for CI, containerization, and environment-based deployments. The few challenges that came up were handled with small, practical decisions that keep the project maintainable. From here, enhancements around CI, a container image, configuration, observability, and a few security features will take it smoothly into DevOps territory without complicating the core.
