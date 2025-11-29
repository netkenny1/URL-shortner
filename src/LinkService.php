<?php

class LinkService {
    private $repository;
    
    public function __construct(LinkRepository $repository) {
        $this->repository = $repository;
    }
    
    public function createLink(string $originalUrl): array {
        UrlValidator::validate($originalUrl);
        
        $shortCode = $this->repository->generateUniqueShortCode();
        $id = $this->repository->create($originalUrl, $shortCode);
        
        return $this->repository->findById($id);
    }
    
    public function getLink(int $id): ?array {
        return $this->repository->findById($id);
    }
    
    public function getAllLinks(int $limit = Constants::DEFAULT_LIMIT): array {
        return $this->repository->findAll($limit);
    }
    
    public function updateLink(int $id, ?string $originalUrl): ?array {
        if ($originalUrl !== null && $originalUrl !== '') {
            UrlValidator::validate($originalUrl);
            $this->repository->updateUrl($id, $originalUrl);
        }
        
        return $this->repository->findById($id);
    }
    
    public function deleteLink(int $id): bool {
        return $this->repository->delete($id);
    }
    
    public function redirect(string $shortCode): ?string {
        $link = $this->repository->findByShortCode($shortCode);
        
        if (!$link) {
            return null;
        }
        
        $this->repository->incrementClickCount($link['id']);
        return $link['original_url'];
    }
}

