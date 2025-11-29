<?php

class HealthChecker {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function check(): array {
        $status = 'healthy';
        $checks = [];
        
        // Database connectivity check
        try {
            $this->pdo->query("SELECT 1");
            $checks['database'] = 'ok';
        } catch (Exception $e) {
            $checks['database'] = 'error: ' . $e->getMessage();
            $status = 'unhealthy';
        }
        
        // Database schema check
        try {
            $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='links'");
            if ($stmt->fetch()) {
                $checks['schema'] = 'ok';
            } else {
                $checks['schema'] = 'error: links table not found';
                $status = 'unhealthy';
            }
        } catch (Exception $e) {
            $checks['schema'] = 'error: ' . $e->getMessage();
            $status = 'unhealthy';
        }
        
        return [
            'status' => $status,
            'timestamp' => date('c'),
            'checks' => $checks,
        ];
    }
}

