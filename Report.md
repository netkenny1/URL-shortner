# ShortKenny URL Shortener
Author: Kenny Tohme  
Course: Software Development and DevOps  
Date: 4 October 2025

## Abstract
This project is a lightweight URL shortener built with PHP and SQLite. It was designed as a simple but complete application to show the full software development life cycle and prepare for DevOps work. Users submit a long URL, get a short link, and when that short link is opened the app redirects to the original URL and tracks how many times it was used. The goal was to build a working tool, follow a clear process, use version control properly, document it well, and keep the codebase ready for CI, containerization, and future scaling.

## 1. Project Overview
The app provides a small browser UI and a REST style API. Paste a long URL and receive a short code. Visiting that short path redirects to the original address and increments a click counter stored in SQLite. There is also an endpoint to list links with stats.

Core features implemented:
- Create short links from valid long URLs
- Redirect from short code to original URL
- Increment click count on each redirect
- List stored links with their statistics through the API or UI
- Minimal front end that calls the same API

## 2. Chosen SDLC Model and Approach
Model: Iterative and incremental.

Reasoning: The scope is small and each feature can be built and tested in short cycles. The order was database and redirect, then API, then UI, then polish. Each iteration gave feedback and kept changes simple, which matches DevOps habits like frequent integration and small updates.

Phases followed:
- Planning: define purpose, features, and constraints like simple local run and no external services
- Requirements: capture functional and non functional items
- Design: map architecture, schema, and routing
- Development: implement in small steps, verify each step before moving on

## 3. Requirements
Functional:
- Accept a long URL and generate a unique short code
- Redirect from short code to original URL
- Increment a stored click counter on each redirect
- Provide an endpoint to list links with stats

Non functional:
- Run locally with only PHP installed
- Use persistent storage with SQLite
- Keep code clean, readable, and modular
- Include a minimal UI to exercise the API

## 4. Architecture and Design
All requests enter through a single front controller. It decides if a request is for the UI, the API, or a redirect.

Key components:
- router.php handles routing and dispatch
- api.php exposes endpoints for create and list
- redirect.php resolves a short code, increments clicks, and issues a 302 redirect
- lib.php contains database connection, schema setup, validation, code generation, and helpers
- SQLite stores original_url, short_code, click_count, created_at, updated_at

Database schema:
- id integer primary key autoincrement
- original_url text not null
- short_code text not null unique
- click_count integer not null default 0
- created_at text not null default datetime('now')
- updated_at text not null default datetime('now')

Request flow:
1) Client sends a request  
2) router.php routes to API or redirect or UI  
3) Handler calls lib.php for data access  
4) SQLite persists and retrieves data

## 5. Implementation
Database and helpers:
- PDO SQLite connection with exceptions enabled
- On first run, ensure links table exists
- Validate URLs with PHP filters
- Generate random alphanumeric codes and retry if a unique constraint fails
- Helpers for create, fetch by code, increment clicks, list links

Routing:
- Root path serves the UI
- Paths under /api go to api.php
- Any single segment not under /api is treated as a short code and handled by redirect.php

API:
- POST /api/links creates a new short link from a JSON body with original_url
- GET /api/links returns recent links with id, original_url, short_code, click_count, created_at, updated_at
- Optionally GET /api/links/{id} returns a single link record
- Optionally DELETE /api/links/{id} deletes a link

Redirect handler:
- Extract short code from path
- Look up row by short_code
- Increment click_count and update updated_at
- Issue 302 Location to original_url
- Return 404 if the code is unknown

UI:
- Plain HTML plus a small script that calls the API
- Input for long URL, button to shorten, and a list of links with counts

Running locally:
1) Ensure PHP is installed  
2) Start the server: php -S 127.0.0.1:8000 router.php  
3) Open http://127.0.0.1:8000

## 6. Testing
Manual checks:
- Create a short link, open it, confirm redirect and click count changes
- Use the API to create and list links and verify fields

Negative tests:
- Submit invalid URLs and confirm clean error responses
- Request a non existent code and confirm a 404

Quick sanity:
- Run php syntax checks across files
- Apply the schema in an in memory SQLite instance to confirm SQL is valid

## 7. Challenges and Solutions
Short code collisions:
- Solved by a unique index on short_code and a retry on insert failure. Small and reliable.

URL validation:
- Use PHP filter validation to block bad inputs. Return clear JSON errors and show them in the UI.

Routing without a framework:
- A front controller with simple path checks kept the app small and easy to follow.

Zero config persistence:
- SQLite removed setup hassles and kept the project portable while still using SQL.

Minimal UI:
- A simple page with fetch requests was enough to demonstrate the API without adding build tools.

## 8. DevOps Scaling Plan
Continuous integration:
- Add a GitHub Actions workflow that runs php -l and a quick schema apply test
- Later add small unit tests for validation and code generation

Containerization:
- Build a Docker image using php 8 apache
- Enable rewrite so requests reach the front controller
- Mount a volume for the SQLite file if persistence across restarts is needed

Configuration and environments:
- Use environment variables for the database DSN
- Keep SQLite for local development and support Postgres for staging or production by switching the DSN in one place

Observability:
- Log redirect events with timestamp and code
- Add a tiny stats endpoint or page to see clicks over time

Security and hygiene:
- Rate limit POST /api/links when creating new links
- Optional authentication for admin actions like custom aliases or delete
- Add link expiry and soft delete for cleanup

Performance:
- Ensure indexes on short_code and created_at
- Consider a read through cache for lookups if traffic grows
- Migrate to Postgres when concurrency needs increase

Team workflow:
- Protect main with required CI checks
- Keep pull requests small with descriptive messages

## 9. Conclusion
The URL shortener meets the assignment goals with a small and clear design. It has a working UI and API, uses SQLite for persistence, and is easy to run. The iterative and incremental approach kept the work manageable and testable. The app is ready for DevOps practices like CI, containerization, and environment based deployments. From here it can grow with security, analytics, and scaling improvements without complicating the core.
