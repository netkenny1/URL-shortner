<?php

use PHPUnit\Framework\TestCase;

class LinkRepositoryTest extends TestCase {
    private $pdo;
    private $repository;
    
    protected function setUp(): void {
        // Use in-memory database for tests
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
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
        
        $this->repository = new LinkRepository($this->pdo);
    }
    
    public function testCreateLink() {
        $id = $this->repository->create('https://example.com', 'test123');
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }
    
    public function testFindByShortCode() {
        $this->repository->create('https://example.com', 'test123');
        $link = $this->repository->findByShortCode('test123');
        $this->assertNotNull($link);
        $this->assertEquals('https://example.com', $link['original_url']);
    }
    
    public function testFindById() {
        $id = $this->repository->create('https://example.com', 'test123');
        $link = $this->repository->findById($id);
        $this->assertNotNull($link);
        $this->assertEquals($id, $link['id']);
    }
    
    public function testIncrementClickCount() {
        $id = $this->repository->create('https://example.com', 'test123');
        $this->repository->incrementClickCount($id);
        $link = $this->repository->findById($id);
        $this->assertEquals(1, $link['click_count']);
    }
    
    public function testShortCodeExists() {
        $this->repository->create('https://example.com', 'test123');
        $this->assertTrue($this->repository->shortCodeExists('test123'));
        $this->assertFalse($this->repository->shortCodeExists('nonexistent'));
    }
    
    public function testGenerateUniqueShortCode() {
        $code1 = $this->repository->generateUniqueShortCode();
        $code2 = $this->repository->generateUniqueShortCode();
        $this->assertNotEquals($code1, $code2);
        $this->assertEquals(Constants::SHORT_CODE_LENGTH, strlen($code1));
    }
    
    public function testUpdateUrl() {
        $id = $this->repository->create('https://example.com', 'test123');
        $updated = $this->repository->updateUrl($id, 'https://newexample.com');
        $this->assertTrue($updated);
        $link = $this->repository->findById($id);
        $this->assertEquals('https://newexample.com', $link['original_url']);
    }
    
    public function testDelete() {
        $id = $this->repository->create('https://example.com', 'test123');
        $deleted = $this->repository->delete($id);
        $this->assertTrue($deleted);
        $link = $this->repository->findById($id);
        $this->assertNull($link);
    }
    
    public function testFindAll() {
        $this->repository->create('https://example1.com', 'test1');
        $this->repository->create('https://example2.com', 'test2');
        $links = $this->repository->findAll();
        $this->assertCount(2, $links);
    }
}

