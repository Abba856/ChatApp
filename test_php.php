<?php
// Simple test script to check if PHP is working and receiving requests
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'POST request received',
        'post_data' => $_POST,
        'files_data' => $_FILES
    ]);
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>PHP Test</title>
    </head>
    <body>
        <h1>PHP Test Form</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="test_field" placeholder="Enter some text"><br><br>
            <input type="file" name="test_file"><br><br>
            <input type="submit" value="Submit">
        </form>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    
                    fetch('test_php.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Success:', data);
                        alert('Server response: ' + JSON.stringify(data, null, 2));
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error: ' + error);
                    });
                });
            });
        </script>
    </body>
    </html>
    <?php
}
?>