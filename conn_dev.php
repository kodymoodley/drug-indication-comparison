<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

$connStr="host=localhost port=5432 dbname=drugcentral user=drugcentral password=[password]";

// Create connection
$conn = pg_connect($connStr) or die("Could not connect");;

?>