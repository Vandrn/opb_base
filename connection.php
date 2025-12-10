<?php

// Database configuration for the first database
$host = "localhost";
$username = "ogqxxpcn_surveysac";
$password = "Deutschland10@";
$database = "ogqxxpcn_surveyssac";

// Create connection to the first database
$conn = mysqli_connect($host, $username, $password, $database);
mysqli_set_charset($conn, "utf8");

// Check connection for the first database
if (!$conn) {
    die("Connection to the first database failed: " . mysqli_connect_error());
}

// Database configuration for the second database
$host2 = "localhost";
$username2 = "ogqxxpcn_opb";
$password2 = "Deutschland10@";
$database2 = "ogqxxpcn_opb";

// Create connection to the second database
$conn2 = mysqli_connect($host2, $username2, $password2, $database2);
mysqli_set_charset($conn2, "utf8");

// Check connection for the second database
if (!$conn2) {
    die("Connection to the second database failed: " . mysqli_connect_error());
}
?>