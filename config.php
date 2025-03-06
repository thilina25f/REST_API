<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rest_api_db";


// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if($conn->connect_error){
    die(json_encode(["message" => "Database connection failed: ". $conn->connect_error]));
}
?>