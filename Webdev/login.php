<?php
session_start();

// Check if user is already logged in, redirect to main page if logged in
if(isset($_SESSION['user_id'])) {
    header("Location: main.php");
    exit();
}

// Include database connection
require_once "config.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form is submitted
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate login credentials
    $query = "SELECT id FROM accounts WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        // If user is found, set session and redirect to main page
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        header("Location: main.php");
        exit();
    } else {
        // If no user found, display error message
        $error = "Invalid username or password";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }

  .container {
    width: 35%;
    margin: auto;
    padding: 16px;
    text-align: center;
    background-color: #f4f4f4;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  .input-field {
    width: 100%;
    padding: 8px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
  }

  .button {
    background-color: #ff5722;
    color: white;
    padding: 10px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
  }

  .button:hover {
    background-color: #e64a19;
  }

  .button-group {
    display: flex;
    justify-content: space-between;
  }

  .button-group .button {
    width: 48%;
  }
</style>
</head>

<body>
  <div class="container">
    <h2>Login</h2>
    <form method="POST" action="adminpanel.php"> <!-- Update action attribute here -->
      <input type="text" id="username" name="username" class="input-field" placeholder="Username" required><br>
      <input type="password" id="password" name="password" class="input-field" placeholder="Password" required><br>
      <button type="submit" name="login" class="button">Login</button><br>
      <div class="button-group">
        <button type="button" class="button" onclick="window.location.href='signup.php'">Sign-up</button>
        <button type="button" class="button" onclick="window.location.href='admin.php'">Admin</button>
      </div>
    </form>
  </div>
</body>

</html>
