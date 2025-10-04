<?php
require __DIR__ . '/db/config.php';
require __DIR__ . '/lib.php';

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$body = json_decode(file_get_contents('php://input'), true) ?? [];

if (preg_match('#^/api/links/?$#', $path)) {
  if ($method === 'GET') {
    $stmt = $pdo->query("SELECT id, original_url, short_code, click_count, created_at, updated_at FROM links ORDER BY id DESC LIMIT 50");
    json_out($stmt->fetchAll());
  }
  if ($method === 'POST') {
    $url = trim($body['original_url'] ?? '');
    if (!preg_match('#^https?://#i', $url)) json_out(['error' => 'Invalid URL. Must start with http or https'], 400);

    // unique short code
    do {
      $code = generate_code(6);
      $check = $pdo->prepare("SELECT id FROM links WHERE short_code = ?");
      $check->execute([$code]);
      $exists = $check->fetch();
    } while ($exists);

    $stmt = $pdo->prepare("INSERT INTO links (original_url, short_code) VALUES (?, ?)");
    $stmt->execute([$url, $code]);

    $id = (int)$pdo->lastInsertId();
    $row = $pdo->query("SELECT id, original_url, short_code, click_count, created_at, updated_at FROM links WHERE id = $id")->fetch();
    json_out($row, 201);
  }
}

if (preg_match('#^/api/links/(\d+)$#', $path, $m)) {
  $id = (int)$m[1];

  if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT id, original_url, short_code, click_count, created_at, updated_at FROM links WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    $row ? json_out($row) : json_out(['error' => 'Not found'], 404);
  }

  if ($method === 'PUT') {
    $url = trim($body['original_url'] ?? '');
    if ($url && !preg_match('#^https?://#i', $url)) json_out(['error' => 'Invalid URL'], 400);
    $stmt = $pdo->prepare("UPDATE links SET original_url = COALESCE(NULLIF(?, ''), original_url) WHERE id = ?");
    $stmt->execute([$url, $id]);
    $stmt = $pdo->prepare("SELECT id, original_url, short_code, click_count, created_at, updated_at FROM links WHERE id = ?");
    $stmt->execute([$id]);
    json_out($stmt->fetch() ?: ['error' => 'Not found'], $stmt->rowCount() ? 200 : 404);
  }

  if ($method === 'DELETE') {
    $stmt = $pdo->prepare("DELETE FROM links WHERE id = ?");
    $stmt->execute([$id]);
    json_out(['ok' => true]);
  }
}

json_out(['error' => 'Route not found'], 404);
