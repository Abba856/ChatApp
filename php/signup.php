<?php
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    session_start();
    include_once "config.php";
    
    // Log that the script is running
    error_log("Signup script started");
    
    // Check database connection
    if (!$conn) {
        echo "Database connection error: " . mysqli_connect_error();
        error_log("Database connection error: " . mysqli_connect_error());
        exit();
    }
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Invalid request method";
        error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
        exit();
    }
    
    // Log POST data (without password for security)
    error_log("POST data received: " . print_r(array_diff_key($_POST, array_flip(['password'])), true));
    error_log("FILES data received: " . print_r($_FILES, true));
    
    // Check if all required fields are provided
    if (!isset($_POST['fname']) || !isset($_POST['lname']) || !isset($_POST['email']) || !isset($_POST['password'])) {
        echo "All input fields are required!";
        error_log("Missing required fields");
        exit();
    }
    
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
        // Validate email format
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            // Check if email already exists
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
            if(mysqli_num_rows($sql) > 0){
                echo "$email - This email already exists!";
                error_log("Email already exists: " . $email);
            }else{
                // Handle image upload
                if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
                    $img_name = $_FILES['image']['name'];
                    $img_type = $_FILES['image']['type'];
                    $tmp_name = $_FILES['image']['tmp_name'];
                    
                    error_log("Processing image upload: " . $img_name);
                    
                    $img_explode = explode('.',$img_name);
                    $img_ext = strtolower(end($img_explode));

                    $extensions = ["jpeg", "png", "jpg"];
                    if(in_array($img_ext, $extensions) === true){
                        $types = ["image/jpeg", "image/jpg", "image/png"];
                        if(in_array($img_type, $types) === true){
                            $time = time();
                            $new_img_name = $time.$img_name;
                            
                            error_log("Image name processed: " . $new_img_name);
                            
                            // Check if images directory exists and is writable
                            $images_dir = '../images';
                            if (!is_dir($images_dir)) {
                                error_log("Images directory does not exist, creating it");
                                // Try to create the directory if it doesn't exist
                                if (!mkdir($images_dir, 0777, true)) {
                                    echo "Failed to create images directory!";
                                    error_log("Failed to create images directory");
                                    exit();
                                }
                            }
                            
                            if(is_writable($images_dir)) {
                                $target_path = $images_dir . "/" . $new_img_name;
                                error_log("Moving file to: " . $target_path);
                                
                                if(move_uploaded_file($tmp_name, $target_path)){
                                    error_log("File moved successfully");
                                    // Generate a unique_id that fits in the int range and is unique
                                    $max_attempts = 10;
                                    $attempt = 0;
                                    $ran_id = 0;
                                    $unique = false;
                                    
                                    while ($attempt < $max_attempts && !$unique) {
                                        // Generate a random number that fits in the int range
                                        $ran_id = mt_rand(10000000, 99999999);
                                        
                                        // Check if this ID is already used
                                        $check_query = mysqli_query($conn, "SELECT unique_id FROM users WHERE unique_id = '{$ran_id}'");
                                        if (mysqli_num_rows($check_query) == 0) {
                                            $unique = true;
                                        }
                                        $attempt++;
                                    }
                                    
                                    if (!$unique) {
                                        echo "Unable to generate unique ID. Please try again.";
                                        error_log("Failed to generate unique ID after $max_attempts attempts");
                                        exit();
                                    }
                                    
                                    $status = "Active now";
                                    $encrypt_pass = md5($password);
                                    
                                    error_log("Inserting user data with unique_id: " . $ran_id);
                                    
                                    // Insert user data into database
                                    $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status)
                                    VALUES ('{$ran_id}', '{$fname}','{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}', '{$status}')");
                                    
                                    if($insert_query){
                                        error_log("User inserted successfully");
                                        // Fetch the inserted user data
                                        $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                        if(mysqli_num_rows($select_sql2) > 0){
                                            $result = mysqli_fetch_assoc($select_sql2);
                                            $_SESSION['unique_id'] = $result['unique_id'];
                                            echo "success";
                                            error_log("Registration successful, redirecting");
                                        }else{
                                            echo "User registration failed. Please try again!";
                                            error_log("Failed to fetch inserted user");
                                        }
                                    }else{
                                        $error = mysqli_error($conn);
                                        echo "Something went wrong during registration. Please try again! Error: " . $error;
                                        error_log("Database insert error: " . $error);
                                    }
                                } else {
                                    echo "Error moving uploaded file! Check directory permissions.";
                                    error_log("Failed to move uploaded file");
                                }
                            } else {
                                echo "Images directory is not writable!";
                                error_log("Images directory is not writable");
                            }
                        }else{
                            echo "Please upload a valid image file (jpeg, png, jpg)";
                            error_log("Invalid image type: " . $img_type);
                        }
                    }else{
                        echo "Please upload a valid image file (jpeg, png, jpg)";
                        error_log("Invalid image extension: " . $img_ext);
                    }
                } else {
                    // Handle file upload errors
                    if (isset($_FILES['image'])) {
                        $error_code = $_FILES['image']['error'];
                        error_log("File upload error code: " . $error_code);
                        
                        switch($error_code) {
                            case UPLOAD_ERR_INI_SIZE:
                                echo "File is too large (server config)";
                                break;
                            case UPLOAD_ERR_FORM_SIZE:
                                echo "File is too large (form limit)";
                                break;
                            case UPLOAD_ERR_PARTIAL:
                                echo "File upload was interrupted";
                                break;
                            case UPLOAD_ERR_NO_FILE:
                                echo "No file was uploaded";
                                break;
                            case UPLOAD_ERR_NO_TMP_DIR:
                                echo "Missing temporary folder";
                                break;
                            case UPLOAD_ERR_CANT_WRITE:
                                echo "Failed to write file to disk";
                                break;
                            case UPLOAD_ERR_EXTENSION:
                                echo "File upload stopped by extension";
                                break;
                            default:
                                echo "Unknown upload error";
                                break;
                        }
                    } else {
                        echo "Please upload an image file!";
                        error_log("No image file provided");
                    }
                }
            }
        }else{
            echo "$email is not a valid email!";
            error_log("Invalid email format: " . $email);
        }
    }else{
        echo "All input fields are required!";
        error_log("Empty fields detected");
    }
?>
