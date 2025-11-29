<?php
require __DIR__ . '/db/config.php';
require __DIR__ . '/src/autoload.php';

// Start metrics collection
MetricsCollector::startRequest();

try {
    $pdo = db();
    $repository = new LinkRepository($pdo);
    $service = new LinkService($repository);
    
    $code = $_GET['code'] ?? '';
    
    if (!ShortCodeGenerator::isValid($code)) {
        MetricsCollector::endRequest(true);
        http_response_code(404);
        echo "Not found";
        exit;
    }
    
    $originalUrl = $service->redirect($code);
    
    if ($originalUrl === null) {
        MetricsCollector::endRequest(true);
        http_response_code(404);
        echo "Not found";
        exit;
    }
    
    // Log the redirect event for observability
    error_log(sprintf("[%s] Redirecting %s to %s", date('Y-m-d H:i:s'), $code, $originalUrl));
    
    MetricsCollector::endRequest(false);
    header("Location: " . $originalUrl, true, 302);
    exit;
    
} catch (Exception $e) {
    MetricsCollector::endRequest(true);
    error_log('Redirect Error: ' . $e->getMessage());
    http_response_code(500);
    echo "Internal server error";
    exit;
}
