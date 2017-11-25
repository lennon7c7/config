<?php
$host = 'localhost';
$username = 'root';
$password = 'root';

// Create connection
$conn = mysqli_connect($host, $username, $password);

// Check connection
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
echo 'Connected successfully';
