<?php
// Script to update the database schema
include_once "php/config.php";

// Check database connection
if (!$conn) {
    die("Database connection error: " . mysqli_connect_error());
}

// Update the unique_id column to VARCHAR to accommodate larger values
$sql = "ALTER TABLE users MODIFY unique_id VARCHAR(255) NOT NULL";

if (mysqli_query($conn, $sql)) {
    echo "Database schema updated successfully. unique_id column changed to VARCHAR(255).
";
} else {
    echo "Error updating database schema: " . mysqli_error($conn) . "
";
}

// Also update the messages table for consistency
$sql2 = "ALTER TABLE messages MODIFY incoming_msg_id VARCHAR(255) NOT NULL, MODIFY outgoing_msg_id VARCHAR(255) NOT NULL";

if (mysqli_query($conn, $sql2)) {
    echo "Messages table updated successfully.
";
} else {
    echo "Error updating messages table: " . mysqli_error($conn) . "
";
}

mysqli_close($conn);
?>
