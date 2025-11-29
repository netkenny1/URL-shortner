<?php

use PHPUnit\Framework\TestCase;

class LinkServiceTest extends TestCase {
    private $pdo;
    private $repository;
    private $service;
    
    protected function setUp(): void {
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
        $this->service = new LinkService($this->repository);
    }
    
    public function testCreateLink() {
        $link = $this->service->createLink('https://example.com');
        $this->assertNotNull($link);
        $this->assertEquals('https://example.com', $link['original_url']);
        $this->assertNotEmpty($link['short_code']);
    }
    
    public function testCreateLinkThrowsExceptionForInvalidUrl() {
        $this->expectException(InvalidArgumentException::class);
        $this->service->createLink('invalid-url');
    }
    
    public function testGetLink() {
        $created = $this->service->createLink('https://example.com');
        $link = $this->service->getLink($created['id']);
        $this->assertNotNull($link);
        $this->assertEquals($created['id'], $link['id']);
    }
    
    public function testGetAllLinks() {
        $this->service->createLink('https://example1.com');
        $this->service->createLink('https://example2.com');
        $links = $this->service->getAllLinks();
        $this->assertCount(2, $links);
    }
    
    public function testUpdateLink() {
        $created = $this->service->createLink('https://example.com');
        $updated = $this->service->updateLink($created['id'], 'https://newexample.com');
        $this->assertNotNull($updated);
        $this->assertEquals('https://newexample.com', $updated['original_url']);
    }
    
    public function testUpdateLinkThrowsExceptionForInvalidUrl() {
        $created = $this->service->createLink('https://example.com');
        $this->expectException(InvalidArgumentException::class);
        $this->service->updateLink($created['id'], 'invalid-url');
    }
    
    public function testDeleteLink() {
        $created = $this->service->createLink('https://example.com');
        $deleted = $this->service->deleteLink($created['id']);
        $this->assertTrue($deleted);
        $link = $this->service->getLink($created['id']);
        $this->assertNull($link);
    }
    
    public function testRedirect() {
        $created = $this->service->createLink('https://example.com');
        $originalUrl = $this->service->redirect($created['short_code']);
        $this->assertEquals('https://example.com', $originalUrl);
        
        // Verify click count was incremented
        $link = $this->service->getLink($created['id']);
        $this->assertEquals(1, $link['click_count']);
    }
    
    public function testRedirectReturnsNullForNonExistentCode() {
        $url = $this->service->redirect('nonexistent');
        $this->assertNull($url);
    }
}

