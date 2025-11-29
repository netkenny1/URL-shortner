<?php

class UrlValidator {
    public static function isValid(string $url): bool {
        $trimmed = trim($url);
        if (empty($trimmed)) {
            return false;
        }
        return (bool) preg_match('#^https?://#i', $trimmed);
    }
    
    public static function validate(string $url): void {
        if (!self::isValid($url)) {
            throw new InvalidArgumentException('Invalid URL. Must start with http or https');
        }
    }
}

