<?php
// Test script to check image paths
include_once "php/config.php";

// Check if images directory exists
if (is_dir('images')) {
    echo "Images directory exists at: " . realpath('images') . "\n";
    
    // List files in the directory
    $files = scandir('images');
    echo "Files in images directory:\n";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- " . $file . " (" . filesize('images/' . $file) . " bytes)\n";
        }
    }
} else {
    echo "Images directory does not exist!\n";
}

// Check a user's image from the database
$sql = mysqli_query($conn, "SELECT img FROM users LIMIT 1");
if (mysqli_num_rows($sql) > 0) {
    $row = mysqli_fetch_assoc($sql);
    $image_file = $row['img'];
    echo "\nSample user image: " . $image_file . "\n";
    
    // Check if the image file exists
    $image_path = 'images/' . $image_file;
    if (file_exists($image_path)) {
        echo "Image file exists at: " . realpath($image_path) . "\n";
        echo "File size: " . filesize($image_path) . " bytes\n";
    } else {
        echo "Image file does not exist at expected path: " . $image_path . "\n";
    }
} else {
    echo "No users found in database\n";
}
?>
