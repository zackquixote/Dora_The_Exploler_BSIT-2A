<?php
$mysqli = new mysqli("localhost", "root", "", "crud_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$queries = [
    "ALTER TABLE barangay_settings DROP FOREIGN KEY fk_brgy_captain",
    "ALTER TABLE barangay_settings DROP FOREIGN KEY fk_brgy_secretary",
    "ALTER TABLE barangay_settings DROP FOREIGN KEY fk_brgy_treasurer",
    "ALTER TABLE barangay_settings DROP COLUMN captain_id, DROP COLUMN secretary_id, DROP COLUMN treasurer_id"
];

foreach ($queries as $query) {
    if ($mysqli->query($query) === TRUE) {
        echo "Successfully executed: $query\n";
    } else {
        echo "Error executing $query: " . $mysqli->error . "\n";
    }
}

$mysqli->close();
?>
