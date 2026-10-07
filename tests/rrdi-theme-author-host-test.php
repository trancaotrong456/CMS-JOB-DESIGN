<?php

function apply_filters($hook, $value) {
    return $value;
}

class RRDI_Test_Theme {
    public function get($field) {
        return 'AuthorURI' === $field ? '' : '';
    }
}

function wp_get_theme() {
    return new RRDI_Test_Theme();
}

require dirname(__DIR__) . '/wp-content/plugins/rara-one-click-demo-import/includes/class-rrdi-main.php';

$warnings = array();
set_error_handler(function ($severity, $message) use (&$warnings) {
    $warnings[] = $message;
    return true;
});

$reflection = new ReflectionClass('RRDI_Theme_Demo_Import');
$importer = $reflection->newInstanceWithoutConstructor();
$result = $importer->is_valid_theme_author();

restore_error_handler();

if (false !== $result) {
    fwrite(STDERR, "FAIL: Theme without AuthorURI must be rejected.\n");
    exit(1);
}

if (!empty($warnings)) {
    fwrite(STDERR, "FAIL: Theme without AuthorURI emitted warning: {$warnings[0]}\n");
    exit(1);
}

echo "PASS: Missing AuthorURI is rejected without warnings.\n";
