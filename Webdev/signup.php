<?php
require_once "config.php";

// Signup
if(isset($_POST['signup'])){
    // Retrieve form data
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];

    // Check if username already exists
    $checkQuery = "SELECT * FROM accounts WHERE username = '$newUsername'";
    $checkResult = $conn->query($checkQuery);

    if($checkResult->num_rows > 0){
        echo "Username already exists. Please choose a different one.";
    } else {
        // Insert new user into the database
        $insertQuery = "INSERT INTO accounts (username, password) VALUES ('$newUsername', '$newPassword')";
        if($conn->query($insertQuery) === TRUE) {
            // Redirect to login page after successful signup
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $insertQuery . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up</title>
<style>
  /* Basic styling */
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f9f9f9; /* changed background color */
  }
  form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
  }
  input[type="text"],
  input[type="password"],
  input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
  }
  input[type="submit"] {
    background-color: orange; /* changed button color */
    color: #fff;
    cursor: pointer;
  }
  input[type="submit"]:hover {
    background-color: #ff8c00; /* slightly darker shade on hover */
  }
</style>
</head>
<body>
  <form method="post">
    <label for="new_username" style="color: orange;">Username:</label><br> <!-- Text color changed to orange -->
    <input type="text" id="new_username" name="new_username"><br>
    <label for="new_password" style="color: orange;">Password:</label><br> <!-- Text color changed to orange -->
    <input type="password" id="new_password" name="new_password"><br><br>
    <input type="submit" value="Sign Up" name="signup">
  </form>
</body>
</html>
