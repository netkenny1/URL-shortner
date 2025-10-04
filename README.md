# ShortKenny - URL Shortener

## 📌 Project Overview
**ShortKenny** is a minimal full-stack web application that allows users to shorten long URLs, redirect visitors through a custom short code, and track the number of clicks for each link.  
It is designed as a lightweight backend-focused project following software development best practices and includes a RESTful API, persistent storage, and a simple user interface.

This project was developed as part of the **DevOps Pipeline Preparation Assignment** and can later be extended with containerization, CI/CD automation, and scalable database integration.

---

## ✨ Core Features
- 🔗 **URL Shortening:** Convert long links into short, shareable URLs.
- 🔄 **Redirection:** Visiting a short code redirects users to the original long URL.
- 📊 **Click Tracking:** Tracks how many times each short link is used.
- 📜 **RESTful API:** Full API to create, read, and manage links.
- 💾 **Persistent Storage:** All data is stored in an SQLite database.

---

## 🛠️ Tech Stack
| Layer         | Technology Used         |
|--------------|--------------------------|
| **Backend**  | PHP 8.4 (Built-in dev server) |
| **Frontend** | HTML, CSS, JavaScript    |
| **Database** | SQLite (lightweight, persistent) |
| **API**      | RESTful endpoints (JSON) |

---

## 📁 Project Structure
├── api.php # REST API endpoints (CRUD operations)
├── index.php # Frontend UI with JavaScript for API calls
├── redirect.php # Handles redirection from short codes
├── router.php # Entry point and request router
├── lib.php # Utility functions and helpers
├── data.sqlite # SQLite database file
├── db/config.php # Database configuration and connection
└── mail/ (optional) # Placeholder for email functionality (not required)

---

## 🚀 Setup Instructions

### 1. Prerequisites
- **PHP** installed (8.0+ recommended). Check with:
  ```bash
  php -v
