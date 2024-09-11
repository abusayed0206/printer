<?php
// Assuming you have a MySQL database setup
$servername = "127.0.0.1";
$port = "3306";
$username = "sayed"; // Your MySQL username
$password = "fsdfsdfs"; // Your MySQL password
$dbname = "fsdfsd"; // Your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the form_data table if it doesn't exist
$sql_create_table = "CREATE TABLE IF NOT EXISTS form_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file VARCHAR(255) NOT NULL,
    studentID VARCHAR(50),
    color VARCHAR(50) NOT NULL,
    sides VARCHAR(50) NOT NULL,
    phoneNumber VARCHAR(20),
    note TEXT,
    paid ENUM('Yes', 'No') DEFAULT 'No',
    timestamp TIMESTAMP
)";

if ($conn->query($sql_create_table) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// File upload directory
$upload_dir = "uploads/";

// Check if the upload directory exists, if not, create it
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Get current timestamp in GMT+6 Asia/Dhaka timezone
date_default_timezone_set('Asia/Dhaka');
$timestamp = date('Y-m-d H:i:s'); // Use 'H' for 24-hour format

// Prepare and bind the SQL statement
$stmt = $conn->prepare("INSERT INTO form_data (file, studentID, color, sides, phoneNumber, note, paid, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

// Array of allowed file extensions
$allowed_extensions = array('pdf', 'doc', 'docx', 'ppt', 'pptx');

// Set parameters and execute for each uploaded file
foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
    $uploaded_file = $_FILES['files']['tmp_name'][$key]; // Temporary file path
    $file_name = $_FILES['files']['name'][$key]; // Original file name
    $ext = pathinfo($file_name, PATHINFO_EXTENSION); // Get file extension

    // Check if the file extension is allowed
    if (!in_array(strtolower($ext), $allowed_extensions)) {
        echo "File type not allowed: $file_name";
        continue; // Skip processing this file
    }

    $studentID = isset($_POST['studentID']) ? $_POST['studentID'] : ''; // Student ID
    $color = $_POST['color']; // Color
    $sides = $_POST['sides']; // Print sides
    $phoneNumber = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : ''; // Phone Number
    $note = isset($_POST['note']) ? $_POST['note'] : ''; // Note
    $paid = 'No'; // Default value for 'Paid' status

    // Generate new file name based on original file name and timestamp
    $new_file_name = $file_name . "_" . $timestamp. "." . $ext; // Appending timestamp to original file name
    $file_path = $upload_dir . $new_file_name; // Final file path

    // Move the uploaded file to the upload directory without renaming
    if (move_uploaded_file($uploaded_file, $file_path)) {
        // File moved successfully, insert data into the database
        $stmt->bind_param("ssssssss", $new_file_name, $studentID, $color, $sides, $phoneNumber, $note, $paid, $timestamp);
        $stmt->execute();
    } else {
        // File upload failed
        echo "Error uploading file: $file_name";
    }
}

echo "<p style='color: green; font-weight: bold;'>অর্ডার করা হয়ে গেছে।  Order again <a href='https://printer.sayed.page/' style='text-decoration: none; color: blue;'>here</a>.</p>";

$stmt->close();
$conn->close();
?>
