<?php
require_once "config.php";

// Check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$username = $_SESSION['username'];

// Fetch user details from the accounts table
$query = "SELECT * FROM accounts WHERE username = '$username'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userId = $row['id'];

    // Fetch associated details from the details table using the user's ID
    $detailsQuery = "SELECT * FROM details WHERE id = '$userId'";
    $detailsResult = $conn->query($detailsQuery);

    if ($detailsResult->num_rows > 0) {
        $details = $detailsResult->fetch_assoc();
        $firstName = $details['FirstName'];
        $lastName = $details['LastName'];
        $contact = $details['Contact'];
        $email = $details['Email'];
    } else {
        // If details not found, initialize variables
        $firstName = "";
        $lastName = "";
        $contact = "";
        $email = "";
    }
} else {
    echo "User not found.";
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['saveChanges'])) {
        // Retrieve form data
        $editFirstName = $_POST['editFirstName'];
        $editLastName = $_POST['editLastName'];
        $editContact = $_POST['editContact'];
        $editEmail = $_POST['editEmail'];

        // Check if details already exist for the user
        $existingDetailsQuery = "SELECT * FROM details WHERE id = '$userId'";
        $existingDetailsResult = $conn->query($existingDetailsQuery);

        if ($existingDetailsResult->num_rows > 0) {
            // Details exist, update the record
            $updateQuery = "UPDATE details SET FirstName = '$editFirstName', LastName = '$editLastName', Contact = '$editContact', Email = '$editEmail' WHERE id = '$userId'";
            if ($conn->query($updateQuery) === TRUE) {
                // Details updated successfully
                // Refresh page to reflect changes
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                echo "Error updating details: " . $conn->error;
            }
        } else {
            // Details don't exist, insert a new record
            $insertQuery = "INSERT INTO details (id, FirstName, LastName, Contact, Email) VALUES ('$userId', '$editFirstName', '$editLastName', '$editContact', '$editEmail')";
            if ($conn->query($insertQuery) === TRUE) {
                // Details inserted successfully
                // Refresh page to reflect changes
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                echo "Error inserting details: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Page</title>
<style>
/* Basic styling */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background-color: #ffffff; /* Background Color */
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header h2 {
    color: #ff5722; /* Button Background Color */
}

.button-container {
    display: flex;
}

.button {
    background-color: #ff5722; /* Button Background Color */
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin-right: 10px;
    cursor: pointer;
    border-radius: 5px;
}

.button:hover {
    background-color: #e64a19; /* Button Hover Background Color */
}

/* Style for popup modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
    padding-top: 60px;
}

.modal-content {
    background-color: #f4f4f4; /* Container Background Color */
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
}

/* Set table border color to grey and 5px thick */
table {
    border-collapse: collapse;
    width: 100%;
    border: 5px solid grey;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

.category-link {
    text-decoration: none;
    color: #ff5722; /* Button Background Color */
    padding: 10px;
    border: 1px solid #ff5722; /* Button Background Color */
    border-radius: 5px;
}

.category-link:hover {
    background-color: #ff5722; /* Button Background Color */
    color: white;
}

</style>
</head>
<body>

<div class="header">
  <div class="button-container">
    <form method="post" action="login.php" style="display: inline;">
</form>
  </div>
</div>

<h2>Welcome, <?php echo $username; ?>!</h2>
<h3>User Details</h3>
<table>
    <tr>
        <td><b>First Name:</b></td>
        <td><?php echo $firstName; ?></td>
    </tr>
    <tr>
        <td><b>Last Name:</b></td>
        <td><?php echo $lastName; ?></td>
    </tr>
    <tr>
        <td><b>Contact:</b></td>
        <td><?php echo $contact; ?></td>
    </tr>
    <tr>
        <td><b>Email:</b></td>
        <td><?php echo $email; ?></td>
    </tr>
</table>
<br>
<button class="button" onclick="openEditModal()">Edit Details</button>

<!-- Popup modal for editing details -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit User Details</h2>
        <form method="post" id="editForm">
            <label for="editFirstName">First Name:</label><br>
            <input type="text" id="editFirstName" name="editFirstName" value="<?php echo $firstName; ?>"><br>
            <label for="editLastName">Last Name:</label><br>
            <input type="text" id="editLastName" name="editLastName" value="<?php echo $lastName; ?>"><br>
            <label for="editContact">Contact:</label><br>
            <input type="text" id="editContact" name="editContact" value="<?php echo $contact; ?>"><br>
            <label for="editEmail">Email:</label><br>
            <input type="email" id="editEmail" name="editEmail" value="<?php echo $email; ?>"><br><br>
            <input type="submit" value="Save Changes" class="button" name="saveChanges">
        </form>
    </div>
</div>

<script>
// Function to open the edit modal
function openEditModal() {
    document.getElementById('editModal').style.display = 'block';
}

// Function to close the edit modal
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    var modal = document.getElementById('editModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

</body>
</html>
