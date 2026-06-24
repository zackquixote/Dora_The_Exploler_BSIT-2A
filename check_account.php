<?php
require 'vendor/autoload.php';
require 'system/bootstrap.php';
$app = \Config\Services::codeigniter();
$app->initialize();
$db = \Config\Database::connect();

$account = $db->table('resident_accounts')->where('email', 'lourdianrentinoakol@gmail.com')->get()->getRowArray();
if (!$account) {
    echo "Account not found in resident_accounts table.\n";
} else {
    echo "Account found!\n";
    echo "Status: " . $account['status'] . "\n";
    echo "Resident ID: " . $account['resident_id'] . "\n";
    
    if (password_verify('lourdian', $account['password_hash'])) {
        echo "Password verification: SUCCESS (Password matches hash)\n";
    } else {
        echo "Password verification: FAILED (Password does not match hash)\n";
    }
}
