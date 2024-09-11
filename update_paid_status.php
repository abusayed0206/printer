<?php
// Assuming you have a MySQL database setup
$servername = "localhost";
$username = "sayed"; // Your MySQL username
$password = "sadasdasdas"; // Your MySQL password
$dbname = "dasds"; // Your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the record ID and new 'Paid' status from the form submission
if (isset($_POST['record_id']) && isset($_POST['paid_status'])) {
    $record_id = $_POST['record_id'];
    $paid_status = $_POST['paid_status'];

    // Prepare and execute the SQL statement to update the 'Paid' status
    $sql_update_paid = "UPDATE form_data SET paid = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update_paid);
    $stmt->bind_param("si", $paid_status, $record_id);
    $stmt->execute();

    // Close statement
    $stmt->close();
}

// Redirect back to the page showing files
header("Location: lrs.php");
exit();
?>
