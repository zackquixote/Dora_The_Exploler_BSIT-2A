<?php
$mysqli = new mysqli("localhost", "root", "", "crud_db");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}
$res = $mysqli->query("SHOW CREATE TABLE officials");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error: " . $mysqli->error;
}
$res = $mysqli->query("SHOW CREATE VIEW officials");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error: " . $mysqli->error;
}
$mysqli->close();
?>
