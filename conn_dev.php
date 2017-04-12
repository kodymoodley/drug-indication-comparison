<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

$connStr="host=localhost port=5432 dbname=drugcentral user=drugcentral password=3asy6oin9";

// Create connection
$conn = pg_connect($connStr) or die("Could not connect");;

// If the connection works this should be shown
//echo "<h1>The answer is 42!</h1>";

?>
