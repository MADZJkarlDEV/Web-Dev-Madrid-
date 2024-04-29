<?php
// Start session and include database connection
session_start();
require_once "config.php";

// Check if admin is not logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin.php"); // Redirect to admin login page if not logged in
    exit();
}

// Fetch users from the account table
$query_users = "SELECT * FROM accounts";
$result_users = $conn->query($query_users);

// Check for errors
if (!$result_users) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <!-- Add any necessary CSS styles -->
</head>
<body>
    <h1>User List</h1>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <!-- Add more columns if needed -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Display user data in a table
            while ($user = $result_users->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . $user['username'] . "</td>";
    
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
