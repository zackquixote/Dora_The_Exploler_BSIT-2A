<?php
require 'vendor/autoload.php';
// Minimal CI4 bootstrap to get DB
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
$paths = new \Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$db = \Config\Database::connect();
$tables = $db->listTables();
foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $fields = $db->getFieldData($table);
    foreach ($fields as $field) {
        echo "  - {$field->name} ({$field->type})\n";
    }
}
