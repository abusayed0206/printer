
<?php
// Assuming you have a MySQL database setup
//no auth added. please add auth before using in production

$servername = "localhost";
$username = "sayed"; // Your MySQL username
$password = "MBmEpufsdfsdsxtqvBeo2rLnYf"; // Your MySQL password
$dbname = "asdfasasdsdfsdfsdsdfsd"; // Your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch table columns dynamically
$sql_columns = "SHOW COLUMNS FROM form_data";
$result_columns = $conn->query($sql_columns);
$columns = array();
if ($result_columns->num_rows > 0) {
    while ($row = $result_columns->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
}

// HTML Table
echo "<table border='1'>";
// Table header
echo "<tr>";
foreach ($columns as $column) {
    echo "<th>" . ucfirst($column) . "</th>"; // Capitalize the first letter of each column
}
echo "<th>Action</th>"; // Additional column for delete action
echo "</tr>";

// Fetch data from form_data table
$sql_data = "SELECT * FROM form_data";
$result_data = $conn->query($sql_data);

// Table data
if ($result_data->num_rows > 0) {
    while ($row = $result_data->fetch_assoc()) {
        echo "<tr>";
        foreach ($columns as $column) {
            // If the current column is 'file', create a clickable link for the file
            if ($column == 'file') {
                echo "<td><a href='uploads/" . $row[$column] . "' target='_blank'>" . $row[$column] . "</a></td>";
            } elseif ($column == 'phoneNumber') {
                // Make the phone number clickable, directing to a WhatsApp message
                $whatsapp_link = "https://wa.me/" . $row[$column] . "?text=আপনার%20মুদ্রণের%20খরচ%20পরিশোধ%20করুন।%20ফাইলে%20নামঃ%20" . $row['file'] . ",%20সময়ঃ%20" . $row['timestamp'];
                echo "<td><a href='$whatsapp_link' target='_blank'>" . $row[$column] . "</a></td>";
            } elseif ($column == 'paid') {
                // Display 'Paid' status as a dropdown menu
                echo "<td>";
                echo "<form method='post' action='update_paid_status.php'>";
                echo "<input type='hidden' name='record_id' value='" . $row['id'] . "'>";
                echo "<select name='paid_status'>";
                echo "<option value='Yes'" . ($row['paid'] == 'Yes' ? ' selected' : '') . ">Yes</option>";
                echo "<option value='No'" . ($row['paid'] == 'No' ? ' selected' : '') . ">No</option>";
                echo "</select>";
                echo "<button type='submit'>Update</button>";
                echo "</form>";
                echo "</td>";
            } else {
                echo "<td>" . $row[$column] . "</td>";
            }
        }
        // Add button to delete record
        echo "<td><form method='post' action='delete_record.php'><input type='hidden' name='record_id' value='" . $row['id'] . "'><button type='submit'>Delete</button></form></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='" . (count($columns) + 1) . "'>No data available</td></tr>";
}
echo "</table>";

$conn->close();
?>
