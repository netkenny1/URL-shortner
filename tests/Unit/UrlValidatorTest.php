<?php

use PHPUnit\Framework\TestCase;

class UrlValidatorTest extends TestCase {
    public function testValidHttpUrl() {
        $this->assertTrue(UrlValidator::isValid('http://example.com'));
    }
    
    public function testValidHttpsUrl() {
        $this->assertTrue(UrlValidator::isValid('https://example.com/path'));
    }
    
    public function testInvalidUrlWithoutProtocol() {
        $this->assertFalse(UrlValidator::isValid('example.com'));
    }
    
    public function testInvalidEmptyUrl() {
        $this->assertFalse(UrlValidator::isValid(''));
        $this->assertFalse(UrlValidator::isValid('   '));
    }
    
    public function testValidateThrowsExceptionForInvalidUrl() {
        $this->expectException(InvalidArgumentException::class);
        UrlValidator::validate('invalid-url');
    }
    
    public function testValidateDoesNotThrowForValidUrl() {
        $this->expectNotToPerformAssertions();
        UrlValidator::validate('https://example.com');
    }
}

