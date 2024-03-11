<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "inspect";

$Conn = new mysqli($servername, $username, $password, $database);

if ($Conn->connect_error) {
    die("Connection failed: " . $Conn->connect_error);
}

$Conn->set_charset("utf8");
?>