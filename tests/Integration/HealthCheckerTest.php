<?php

use PHPUnit\Framework\TestCase;

class HealthCheckerTest extends TestCase {
    private $pdo;
    private $checker;
    
    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        $this->checker = new HealthChecker($this->pdo);
    }
    
    public function testCheckReturnsHealthyWhenDatabaseIsOk() {
        $this->pdo->exec("
            CREATE TABLE links(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                original_url TEXT NOT NULL,
                short_code TEXT NOT NULL UNIQUE,
                click_count INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");
        
        $health = $this->checker->check();
        $this->assertEquals('healthy', $health['status']);
        $this->assertEquals('ok', $health['checks']['database']);
        $this->assertEquals('ok', $health['checks']['schema']);
    }
    
    public function testCheckReturnsUnhealthyWhenTableMissing() {
        $health = $this->checker->check();
        $this->assertEquals('unhealthy', $health['status']);
        $this->assertStringContainsString('not found', $health['checks']['schema']);
    }
}

