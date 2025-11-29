<?php

use PHPUnit\Framework\TestCase;

class MetricsCollectorTest extends TestCase {
    protected function setUp(): void {
        MetricsCollector::reset();
    }
    
    public function testStartRequestIncrementsCount() {
        MetricsCollector::startRequest();
        MetricsCollector::endRequest();
        $metrics = MetricsCollector::getMetrics();
        $this->assertEquals(1, $metrics['request_count']);
    }
    
    public function testEndRequestWithErrorIncrementsErrorCount() {
        MetricsCollector::startRequest();
        MetricsCollector::endRequest(true);
        $metrics = MetricsCollector::getMetrics();
        $this->assertEquals(1, $metrics['error_count']);
        $this->assertEquals(0, $metrics['success_count']);
    }
    
    public function testEndRequestWithoutErrorIncrementsSuccessCount() {
        MetricsCollector::startRequest();
        MetricsCollector::endRequest(false);
        $metrics = MetricsCollector::getMetrics();
        $this->assertEquals(0, $metrics['error_count']);
        $this->assertEquals(1, $metrics['success_count']);
    }
    
    public function testGetPrometheusMetricsReturnsValidFormat() {
        MetricsCollector::startRequest();
        MetricsCollector::endRequest();
        $output = MetricsCollector::getPrometheusMetrics();
        $this->assertStringContainsString('http_requests_total', $output);
        $this->assertStringContainsString('http_errors_total', $output);
        $this->assertStringContainsString('http_request_duration_ms', $output);
    }
    
    public function testResetClearsAllMetrics() {
        MetricsCollector::startRequest();
        MetricsCollector::endRequest();
        MetricsCollector::reset();
        $metrics = MetricsCollector::getMetrics();
        $this->assertEquals(0, $metrics['request_count']);
        $this->assertEquals(0, $metrics['error_count']);
    }
}

