<?php

class LinkRepository {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function findByShortCode(string $code): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT id, original_url, short_code, click_count, created_at, updated_at 
             FROM links WHERE short_code = ?"
        );
        $stmt->execute([$code]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT id, original_url, short_code, click_count, created_at, updated_at 
             FROM links WHERE id = ?"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function findAll(int $limit = Constants::DEFAULT_LIMIT): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, original_url, short_code, click_count, created_at, updated_at 
             FROM links ORDER BY id DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function create(string $originalUrl, string $shortCode): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO links (original_url, short_code) VALUES (?, ?)"
        );
        $stmt->execute([$originalUrl, $shortCode]);
        return (int) $this->pdo->lastInsertId();
    }
    
    public function updateUrl(int $id, string $originalUrl): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE links SET original_url = ?, updated_at = datetime('now') WHERE id = ?"
        );
        $stmt->execute([$originalUrl, $id]);
        return $stmt->rowCount() > 0;
    }
    
    public function incrementClickCount(int $id): void {
        $stmt = $this->pdo->prepare(
            "UPDATE links SET click_count = click_count + 1, updated_at = datetime('now') WHERE id = ?"
        );
        $stmt->execute([$id]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM links WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
    
    public function shortCodeExists(string $code): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM links WHERE short_code = ?");
        $stmt->execute([$code]);
        return (bool) $stmt->fetch();
    }
    
    public function generateUniqueShortCode(int $length = Constants::SHORT_CODE_LENGTH): string {
        do {
            $code = ShortCodeGenerator::generate($length);
        } while ($this->shortCodeExists($code));
        
        return $code;
    }
}

