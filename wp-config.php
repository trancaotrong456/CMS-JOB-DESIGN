<?php
/**
 * Stable, tracked WordPress configuration entry point.
 * Machine-specific settings belong in the ignored wp-config.local.php.
 */
$local_config = __DIR__ . '/wp-config.local.php';
if (!is_file($local_config)) {
    $message = 'Missing wp-config.local.php. Run: powershell -ExecutionPolicy Bypass -File scripts/wamp64-team.ps1 -Action Setup';
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $message . PHP_EOL);
    } else {
        http_response_code(500);
        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    }
    exit(1);
}
require_once $local_config;
