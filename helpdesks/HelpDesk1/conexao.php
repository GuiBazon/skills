<?php

error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = new mysqli('localhost', 'root', 'senai@604', 'seletiva_web_bazon');
if ($mysqli->connect_errno) {
    throw new RuntimeException('mysqli connection error: ' . $mysqli->connect_error);
}

/* Set the desired charset after establishing a connection */
$mysqli->set_charset('utf8mb4');
if ($mysqli->errno) {
    throw new RuntimeException('mysqli error: ' . $mysqli->error);
}

?>