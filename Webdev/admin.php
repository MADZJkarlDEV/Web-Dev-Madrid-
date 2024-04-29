<?php
require_once "config.php";

// Admin Login
if(isset($_POST['admin_login'])){
    // Check login credentials for admin
    $username = $_POST['admin_username'];
    $password = $_POST['admin_password'];
    
    // Check if the user exists in the admin table
    $admin_query = "SELECT * FROM admin WHERE User = '$username' AND Password = '$password'";
    $admin_result = $conn->query($admin_query);
    
    if($admin_result->num_rows > 0){
        // Admin login successful
        session_start();
        $_SESSION['admin_username'] = $username;
        header("Location: adminpanel.php"); // Redirect to admin panel
        exit();
    } else {
        // Check if the user exists in the accounts table
        $account_query = "SELECT * FROM accounts WHERE Username = '$username' AND Password = '$password'";
        $account_result = $conn->query($account_query);
        
        if($account_result->num_rows > 0){
            // Account login successful
            session_start();
            $_SESSION['account_username'] = $username;
            header("Location: accountpanel.php"); // Redirect to account panel
            exit();
        } else {
            echo "<script>alert('Invalid username or password.');</script>"; // Error message
        }
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
    <h2>Admin or Account Login</h2>
    <form method="POST" action="admin.php">
      <div style="text-align: center;">
        <input type="text" id="username" name="admin_username" class="input-field" placeholder="Username" required><br>
        <input type="password" id="password" name="admin_password" class="input-field" placeholder="Password" required><br>
        <button type="submit" name="admin_login" class="button">Login</button><br>
      </div>
    </form>
  </div>
</body>

</html>
