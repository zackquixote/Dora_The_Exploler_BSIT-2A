<?php
$mysqli = new mysqli("localhost", "root", "", "crud_db");
$res = $mysqli->query("SHOW TRIGGERS");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
$mysqli->close();
?>
