<?php
$host = "db";
$user = "root";
$pass = "root";
$db   = "claims_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>