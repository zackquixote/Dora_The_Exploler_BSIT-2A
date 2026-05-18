<?php
require 'public/index.php';

$db = \Config\Database::connect();
try {
    $builder = $db->table('resident_accounts');
    $user = $builder->where('email', 'juan@example.com')->get()->getRowArray();
    if ($user) {
        echo "User found!\n";
        echo "Status: " . $user['status'] . "\n";
        echo "Hash: " . $user['password_hash'] . "\n";
        echo "Match 'password123': " . (password_verify('password123', $user['password_hash']) ? 'Yes' : 'No') . "\n";
        echo "Match 'password': " . (password_verify('password', $user['password_hash']) ? 'Yes' : 'No') . "\n";
    } else {
        echo "User NOT found in database.\n";
        echo "Tables in DB: \n";
        print_r($db->listTables());
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
