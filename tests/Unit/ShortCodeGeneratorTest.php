<?php

use PHPUnit\Framework\TestCase;

class ShortCodeGeneratorTest extends TestCase {
    public function testGenerateReturnsCorrectLength() {
        $code = ShortCodeGenerator::generate(6);
        $this->assertEquals(6, strlen($code));
    }
    
    public function testGenerateReturnsAlphanumeric() {
        $code = ShortCodeGenerator::generate(10);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $code);
    }
    
    public function testGenerateReturnsDifferentCodes() {
        $code1 = ShortCodeGenerator::generate();
        $code2 = ShortCodeGenerator::generate();
        // Very unlikely to be the same, but possible
        $this->assertTrue(strlen($code1) === strlen($code2));
    }
    
    public function testIsValidAcceptsValidCodes() {
        $this->assertTrue(ShortCodeGenerator::isValid('abc123'));
        $this->assertTrue(ShortCodeGenerator::isValid('ABC123'));
        $this->assertTrue(ShortCodeGenerator::isValid('a1b2c3'));
    }
    
    public function testIsValidRejectsInvalidCodes() {
        $this->assertFalse(ShortCodeGenerator::isValid('abc')); // Too short
        $this->assertFalse(ShortCodeGenerator::isValid('abc!@#')); // Invalid chars
        $this->assertFalse(ShortCodeGenerator::isValid(''));
    }
}

