<?php
// Database setup script
$hostname = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($hostname, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS chatapp";
if ($conn->query($sql) === TRUE) {
    echo "Database chatapp created successfully or already exists\n";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db("chatapp");

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_id` int(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table users created successfully or already exists\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// Create messages table
$sql = "CREATE TABLE IF NOT EXISTS `messages` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_msg_id` int(255) NOT NULL,
  `outgoing_msg_id` int(255) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  PRIMARY KEY (`msg_id`)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table messages created successfully or already exists\n";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
echo "Database setup completed!\n";
?>
