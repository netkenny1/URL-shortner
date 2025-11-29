<?php
require __DIR__ . '/../db/config.php';

try {
    // Initialize database (this should create the table if it doesn't exist)
    $pdo = db();
    
    // Verify table existence
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='links'");
    if ($stmt->fetch()) {
        echo "Schema test passed: links table exists.\n";
        exit(0);
    } else {
        echo "Schema test failed: links table does not exist.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "Schema test error: " . $e->getMessage() . "\n";
    exit(1);
}

