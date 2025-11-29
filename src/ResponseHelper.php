<?php

class ResponseHelper {
    public static function json(array $data, int $statusCode = Constants::HTTP_OK): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    public static function error(string $message, int $statusCode = Constants::HTTP_BAD_REQUEST): void {
        self::json(['error' => $message], $statusCode);
    }
    
    public static function notFound(string $message = 'Not found'): void {
        self::error($message, Constants::HTTP_NOT_FOUND);
    }
}

