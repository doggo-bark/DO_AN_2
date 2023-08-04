<?php


// Connect to the MySQL server
$db = new mysqli('localhost', 'root', '');

// Check for a successful connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Run the SQL script using the multi_query method
$sql = file_get_contents('shopping.sql');
if ($db->multi_query($sql)) { 
    echo "Database and tables created successfully";
} else {
    echo "Error creating database and tables: " . $db->error;
}

// Close the database connection
$db->close();
?>
