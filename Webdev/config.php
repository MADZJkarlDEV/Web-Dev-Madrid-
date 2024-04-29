<?php

$servername = "172.16.0.214";


$username = "group5";
$password = "12345"; 

$database = "users"; 


$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
