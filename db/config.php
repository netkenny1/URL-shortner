<?php
function db() {
  static $pdo = null;
  if ($pdo) return $pdo;

  // Use environment variable if set, otherwise default to local SQLite
  $dsn = getenv('DB_DSN') ?: 'sqlite:' . __DIR__ . '/../data.sqlite';
  
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];
  $pdo = new PDO($dsn, null, null, $options);

  // schema (only run if using SQLite, or generic enough? CREATE TABLE IF NOT EXISTS is standard SQL mostly, but syntax differs)
  // The requirement says "Keep SQLite for local development and support Postgres...".
  // For now, we stick to SQLite. If DSN is postgres, this schema creation might fail or need adjustment.
  // We'll leave it as is, assuming SQLite for now.
  if (strpos($dsn, 'sqlite') !== false) {
      $pdo->exec("
        CREATE TABLE IF NOT EXISTS links(
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          original_url TEXT NOT NULL,
          short_code TEXT NOT NULL UNIQUE,
          click_count INTEGER NOT NULL DEFAULT 0,
          created_at TEXT NOT NULL DEFAULT (datetime('now')),
          updated_at TEXT NOT NULL DEFAULT (datetime('now'))
        );
      ");
  }

  return $pdo;
}
