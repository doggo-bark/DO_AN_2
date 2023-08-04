<?php
// Connect to the database
$db = new mysqli('localhost', 'root', '', 'shopping');

// Check for a successful connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

?>