<?php

class MetricsCollector {
    private static $requestCount = 0;
    private static $errorCount = 0;
    private static $requestLatencies = [];
    private static $startTime = null;
    
    public static function startRequest(): void {
        self::$startTime = microtime(true);
        self::$requestCount++;
    }
    
    public static function endRequest(bool $isError = false): void {
        if (self::$startTime !== null) {
            $latency = (microtime(true) - self::$startTime) * 1000; // Convert to milliseconds
            self::$requestLatencies[] = $latency;
            
            // Keep only last 1000 latencies to avoid memory issues
            if (count(self::$requestLatencies) > 1000) {
                array_shift(self::$requestLatencies);
            }
        }
        
        if ($isError) {
            self::$errorCount++;
        }
        
        self::$startTime = null;
    }
    
    public static function getMetrics(): array {
        $latencies = self::$requestLatencies;
        $avgLatency = empty($latencies) ? 0 : array_sum($latencies) / count($latencies);
        $p95Latency = self::calculatePercentile($latencies, 95);
        $p99Latency = self::calculatePercentile($latencies, 99);
        
        return [
            'request_count' => self::$requestCount,
            'error_count' => self::$errorCount,
            'success_count' => self::$requestCount - self::$errorCount,
            'average_latency_ms' => round($avgLatency, 2),
            'p95_latency_ms' => round($p95Latency, 2),
            'p99_latency_ms' => round($p99Latency, 2),
        ];
    }
    
    public static function getPrometheusMetrics(): string {
        $metrics = self::getMetrics();
        
        $output = "# HELP http_requests_total Total number of HTTP requests\n";
        $output .= "# TYPE http_requests_total counter\n";
        $output .= "http_requests_total " . $metrics['request_count'] . "\n\n";
        
        $output .= "# HELP http_errors_total Total number of HTTP errors\n";
        $output .= "# TYPE http_errors_total counter\n";
        $output .= "http_errors_total " . $metrics['error_count'] . "\n\n";
        
        $output .= "# HELP http_request_duration_ms Average request latency in milliseconds\n";
        $output .= "# TYPE http_request_duration_ms gauge\n";
        $output .= "http_request_duration_ms " . $metrics['average_latency_ms'] . "\n\n";
        
        $output .= "# HELP http_request_duration_p95_ms 95th percentile request latency in milliseconds\n";
        $output .= "# TYPE http_request_duration_p95_ms gauge\n";
        $output .= "http_request_duration_p95_ms " . $metrics['p95_latency_ms'] . "\n\n";
        
        $output .= "# HELP http_request_duration_p99_ms 99th percentile request latency in milliseconds\n";
        $output .= "# TYPE http_request_duration_p99_ms gauge\n";
        $output .= "http_request_duration_p99_ms " . $metrics['p99_latency_ms'] . "\n";
        
        return $output;
    }
    
    private static function calculatePercentile(array $values, float $percentile): float {
        if (empty($values)) {
            return 0;
        }
        
        sort($values);
        $index = ceil(count($values) * ($percentile / 100)) - 1;
        return $values[max(0, (int)$index)];
    }
    
    public static function reset(): void {
        self::$requestCount = 0;
        self::$errorCount = 0;
        self::$requestLatencies = [];
        self::$startTime = null;
    }
}

