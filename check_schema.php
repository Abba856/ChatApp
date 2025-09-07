<?php
// Script to check and update the database schema if needed
include_once "php/config.php";

// Check database connection
if (!$conn) {
    die("Database connection error: " . mysqli_connect_error());
}

echo "Checking database schema...\n";

// Check current column types
$table_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'unique_id'");
if ($table_check && mysqli_num_rows($table_check) > 0) {
    $row = mysqli_fetch_assoc($table_check);
    $current_type = $row['Type'];
    echo "Current unique_id column type: " . $current_type . "\n";
    
    // If it's still an INT type, we need to change it
    if (strpos($current_type, 'int') !== false && strpos($current_type, 'varchar') === false) {
        echo "Updating unique_id column to VARCHAR(255)...\n";
        
        // Update the unique_id column to VARCHAR to accommodate larger values
        $sql = "ALTER TABLE users MODIFY unique_id VARCHAR(255) NOT NULL";
        
        if (mysqli_query($conn, $sql)) {
            echo "Database schema updated successfully. unique_id column changed to VARCHAR(255).\n";
        } else {
            echo "Error updating database schema: " . mysqli_error($conn) . "\n";
        }
        
        // Also update the messages table for consistency
        $sql2 = "ALTER TABLE users MODIFY incoming_msg_id VARCHAR(255) NOT NULL, MODIFY outgoing_msg_id VARCHAR(255) NOT NULL";
        
        if (mysqli_query($conn, $sql2)) {
            echo "Messages table updated successfully.\n";
        } else {
            echo "Error updating messages table: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "unique_id column is already VARCHAR type. No changes needed.\n";
    }
} else {
    echo "Could not determine current column type.\n";
}

// Close connection
mysqli_close($conn);
echo "Schema check complete.\n";
?>