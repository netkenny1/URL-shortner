<?php

class ShortCodeGenerator {
    private const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private const DEFAULT_LENGTH = 6;
    
    public static function generate(int $length = null): string {
        if ($length === null) {
            $length = class_exists('Constants') ? Constants::SHORT_CODE_LENGTH : self::DEFAULT_LENGTH;
        }
        $code = '';
        $alphabetLength = strlen(self::ALPHABET);
        
        for ($i = 0; $i < $length; $i++) {
            $code .= self::ALPHABET[random_int(0, $alphabetLength - 1)];
        }
        
        return $code;
    }
    
    public static function isValid(string $code): bool {
        $length = strlen($code);
        $minLength = class_exists('Constants') ? Constants::MIN_SHORT_CODE_LENGTH : 4;
        $maxLength = class_exists('Constants') ? Constants::MAX_SHORT_CODE_LENGTH : 32;
        return $length >= $minLength 
            && $length <= $maxLength
            && preg_match('/^[A-Za-z0-9_-]+$/', $code);
    }
}

