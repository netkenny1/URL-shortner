<?php
// Legacy functions for backward compatibility
// New code should use the classes in src/

require __DIR__ . '/src/autoload.php';

function generate_code($len = 6) {
  return ShortCodeGenerator::generate($len);
}

function json_out($data, $status = 200) {
  ResponseHelper::json($data, $status);
}
