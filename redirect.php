<?php
require __DIR__ . '/db/config.php';
$pdo = db();

$code = $_GET['code'] ?? '';
$stmt = $pdo->prepare("SELECT id, original_url FROM links WHERE short_code = ?");
$stmt->execute([$code]);
$link = $stmt->fetch();
if (!$link) { http_response_code(404); echo "Not found"; exit; }

$pdo->prepare("UPDATE links SET click_count = click_count + 1, updated_at = datetime('now') WHERE id = ?")->execute([$link['id']]);

header("Location: " . $link['original_url'], true, 302);
exit;
