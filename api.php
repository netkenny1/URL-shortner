<?php
require __DIR__ . '/db/config.php';
require __DIR__ . '/lib.php';
require __DIR__ . '/src/autoload.php';

// Start metrics collection
MetricsCollector::startRequest();

try {
    $pdo = db();
    $repository = new LinkRepository($pdo);
    $service = new LinkService($repository);
    
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $isError = false;
    
    // Health endpoint
    if ($path === '/health') {
        $healthChecker = new HealthChecker($pdo);
        $health = $healthChecker->check();
        $statusCode = $health['status'] === 'healthy' ? Constants::HTTP_OK : Constants::HTTP_INTERNAL_ERROR;
        ResponseHelper::json($health, $statusCode);
    }
    
    // Metrics endpoint
    if ($path === '/metrics') {
        header('Content-Type: text/plain');
        echo MetricsCollector::getPrometheusMetrics();
        exit;
    }
    
    // API endpoints
    if (preg_match('#^/api/links/?$#', $path)) {
        if ($method === 'GET') {
            $links = $service->getAllLinks();
            ResponseHelper::json($links);
        }
        
        if ($method === 'POST') {
            try {
                $url = trim($body['original_url'] ?? '');
                $link = $service->createLink($url);
                ResponseHelper::json($link, Constants::HTTP_CREATED);
            } catch (InvalidArgumentException $e) {
                $isError = true;
                ResponseHelper::error($e->getMessage(), Constants::HTTP_BAD_REQUEST);
            } catch (Exception $e) {
                $isError = true;
                ResponseHelper::error('Internal server error', Constants::HTTP_INTERNAL_ERROR);
            }
        }
    }
    
    if (preg_match('#^/api/links/(\d+)$#', $path, $m)) {
        $id = (int)$m[1];
        
        if ($method === 'GET') {
            $link = $service->getLink($id);
            if ($link) {
                ResponseHelper::json($link);
            } else {
                $isError = true;
                ResponseHelper::notFound();
            }
        }
        
        if ($method === 'PUT') {
            try {
                $url = trim($body['original_url'] ?? '');
                $link = $service->updateLink($id, $url ?: null);
                if ($link) {
                    ResponseHelper::json($link);
                } else {
                    $isError = true;
                    ResponseHelper::notFound();
                }
            } catch (InvalidArgumentException $e) {
                $isError = true;
                ResponseHelper::error($e->getMessage(), Constants::HTTP_BAD_REQUEST);
            }
        }
        
        if ($method === 'DELETE') {
            $deleted = $service->deleteLink($id);
            if ($deleted) {
                ResponseHelper::json(['ok' => true]);
            } else {
                $isError = true;
                ResponseHelper::notFound();
            }
        }
    }
    
    $isError = true;
    ResponseHelper::notFound('Route not found');
    
} catch (Exception $e) {
    $isError = true;
    error_log('API Error: ' . $e->getMessage());
    ResponseHelper::error('Internal server error', Constants::HTTP_INTERNAL_ERROR);
} finally {
    MetricsCollector::endRequest($isError);
}
