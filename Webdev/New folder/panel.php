<?php
session_start();

// Check if user is not logged in, redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once "config.php";

// Fetch user details from the accounts table
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM accounts WHERE id = '$user_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $username = $user['username'];
} else {
    echo "User not found.";
    exit();
}

// Fetch image filenames from the selected_images table
$query_images = "SELECT image_name FROM selected_images";
$result_images = $conn->query($query_images);

// Check for errors
if (!$result_images) {
    die("Query failed: " . $conn->error);
}

// Count the number of images available
$num_images = $result_images->num_rows;

$query_posters = "SELECT image_name FROM selected_poster_images";
$result_posters = $conn->query($query_posters);

// Check for errors
if (!$result_posters) {
    die("Query failed: " . $conn->error);
}

// Count the number of poster images available
$num_posters = $result_posters->num_rows;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Health Department</title>
    <style>
   body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #ffffff; /* Background Color */
}

.container {
    padding: 20px;
    background-color: #f4f4f4; /* Container Background Color */
}

header {
    text-align: center;
    margin-bottom: 20px;
}

h1 {
    color: #333;
}

.announcement-carousel, .navigation, .posters-grid, .admin-actions {
    margin-bottom: 20px;
}

.announcement-carousel {
    position: relative;
    overflow: hidden;
}

.announcement-carousel .slides {
    display: flex;
}

.announcement-carousel .slide {
    flex: 1 0 100%;
}

.announcement-carousel .slide img {
    display: block; /* Ensure images are block elements */
    margin: 0 auto; /* Center the images horizontally */
    max-width: 100%; /* Ensure images don't exceed the width of their container */
    height: auto;
}

.announcement-carousel .prev,
.announcement-carousel .next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    padding: 10px;
    cursor: pointer;
}

.announcement-carousel .prev {
    left: 0;
}

.announcement-carousel .next {
    right: 0;
}

.navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background-color: #b3ffb3; /* Button Background Color */
}

.navigation-item {
    padding: 10px;
    background-color: #ff5722; /* Button Background Color */
    color: white;
    border-radius: 5px;
    cursor: pointer;
}

.navigation-item:hover {
    background-color: #e64a19; /* Button Hover Background Color */
}

.posters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(600px, 1fr));
    gap: 20px;
}

.posters-grid .poster {
    background-color: #f0f0f0;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
    max-width: 100%; /* Ensure posters don't exceed their container */
    overflow: hidden; /* Hide any content that might overflow */
}

.posters-grid .poster img {
    max-width: 100%; /* Ensure images don't exceed their container */
    max-height: 100%; /* Ensure images don't exceed their container */
    margin: auto; /* Center the images horizontally */
    display: block; /* Ensure images are block elements */
    border-radius: 5px;
}

.admin-actions button {
    padding: 10px;
    background-color: #ff5722; /* Button Background Color */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

#upload-popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

#upload-popup label {
    display: block;
    margin-bottom: 10px;
}

#upload-popup input[type="file"] {
    display: none;
}

#upload-popup .upload-area {
    border: 2px dashed #ccc;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    margin-bottom: 20px;
}

#upload-popup .upload-area:hover {
    background-color: #f0f0f0;
}

.hidden {
    display: none;
}

.navigation{
background-color: #ff5702; 
}
    </style>
</head>
<body>
    <div class="container">
        <h1>Health Department</h1>
        <div class="announcement-carousel">
            <div class="slides">
                <?php
                // Loop through fetched image filenames and generate <img> tags
                if ($result_images->num_rows > 0) {
                    while($image = $result_images->fetch_assoc()) {
                        echo '<div class="slide">';
                        echo '<img src="Announcements/' . $image['image_name'] . '" alt="Announcement">';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="prev" onclick="changeSlide(-1)">❮</div>
            <div class="next" onclick="changeSlide(1)">❯</div>
        </div>

        <div class="navigation">
            <div class="navigation-item" onclick="openPopup('user')">User</div>
            <div class="navigation-item" onclick="openPopup('calendar')">Calendar</div>
            <div class="navigation-item" onclick="openPopup('hot-line')">Hot-Line</div>
            <div class="navigation-item" onclick="window.location.href='login.php'">Logout</div>
        </div>
        <div class="posters-grid">
    <?php
    // Loop through fetched poster filenames and generate HTML for posters
    if ($result_posters->num_rows > 0) {
        $posterCount = 1;
        while($poster = $result_posters->fetch_assoc()) {
            echo '<div class="poster">Poster ' . $posterCount . '<br>';
            echo '<img src="Posters/' . $poster['image_name'] . '" alt="Poster">';
            echo '</div>';
            $posterCount++;
            // Display maximum 6 posters
            if ($posterCount > 6) {
                break;
            }
        }
    }
    ?>
</div>
        <div class="admin-actions">
            <h2>Welcome, <?php echo $admin_name; ?></h2>
        </div>
    </div>
</body>
</html>
