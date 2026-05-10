<?php
$mysqli = new mysqli("localhost", "root", "", "crud_db");
$res = $mysqli->query("DESCRIBE barangay_settings");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
echo "-----\n";
$res = $mysqli->query("SHOW CREATE TABLE barangay_settings");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
