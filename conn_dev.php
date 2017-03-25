<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

$servername = "localhost:5432";
$username = "postgres";
$password = "krsna108";
$database = "drugcentral";

//$servername = "localhost";
//$username = "live";
//$password = "@live1";
//$database = "hiyer";

// Create connection
$conn = new mysqli($servername, $username, $password);
mysqli_select_db($conn,$database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

//echo "Connected successfully";

?>