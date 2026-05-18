<?php
$db = new mysqli('localhost', 'root', '', 'brgy_db');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$result = $db->query("DESCRIBE tbl_logs");
$schema = [];
while ($row = $result->fetch_assoc()) {
    $schema[] = $row;
}
echo json_encode($schema);
