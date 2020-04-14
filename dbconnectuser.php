<?php


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'aditya');
define('DB_PASSWORD', 'rootpassword');
define('DB_NAME', 'bookmoviesdb');
 
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if(!$link){
        die("ERROR: Could not connect to database. "    . mysqli_connect_error());
}

// echo "Database Connected"

?>