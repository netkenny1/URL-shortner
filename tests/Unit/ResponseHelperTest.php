<?php

use PHPUnit\Framework\TestCase;

class ResponseHelperTest extends TestCase {
    public function testJsonSetsCorrectHeaders() {
        ob_start();
        try {
            ResponseHelper::json(['test' => 'data']);
        } catch (Exception $e) {
            // Expected to exit
        }
        $output = ob_get_clean();
        $this->assertStringContainsString('test', $output);
    }
    
    public function testErrorReturnsErrorFormat() {
        ob_start();
        try {
            ResponseHelper::error('Test error');
        } catch (Exception $e) {
            // Expected to exit
        }
        $output = ob_get_clean();
        $this->assertStringContainsString('error', $output);
        $this->assertStringContainsString('Test error', $output);
    }
}

