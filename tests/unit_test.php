<?php
require __DIR__ . '/../lib.php';

$code = generate_code(6);
if (strlen($code) === 6 && preg_match('/^[a-zA-Z0-9]+$/', $code)) {
    echo "Unit test passed: generate_code produces 6-char alphanumeric string.\n";
    exit(0);
} else {
    echo "Unit test failed: generate_code output '$code' is invalid.\n";
    exit(1);
}

