<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: admin.php"); // Redirect to admin login page if not logged in
    exit();
}

// Include database connection
require_once "config.php";

// Fetch user details from the accounts table
$username = $_SESSION['username'];
$query = "SELECT * FROM accounts WHERE username = '$username'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['id']; // Fetch user's id
} else {
    echo "User not found.";
    exit();
}

// Image upload handling for Posters
if(isset($_FILES['file-upload-poster'])) {
    $target_dir_poster = "Posters/";
    $target_file_poster = $target_dir_poster . basename($_FILES["file-upload-poster"]["name"]);
    $uploadOk_poster = 1;
    $imageFileType_poster = strtolower(pathinfo($target_file_poster,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check_poster = getimagesize($_FILES["file-upload-poster"]["tmp_name"]);
        if($check_poster !== false) {
            echo "File is an image - " . $check_poster["mime"] . ".";
            $uploadOk_poster = 1;
        } else {
            echo "File is not an image.";
            $uploadOk_poster = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file_poster)) {
        echo "Sorry, file already exists.";
        $uploadOk_poster = 0;
    }
    // Check file size
    if ($_FILES["file-upload-poster"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk_poster = 0;
    }
    // Allow certain file formats
    if($imageFileType_poster != "jpg" && $imageFileType_poster != "png" && $imageFileType_poster != "jpeg"
    && $imageFileType_poster != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk_poster = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk_poster == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["file-upload-poster"]["tmp_name"], $target_file_poster)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["file-upload-poster"]["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Image upload handling for Announcements
if(isset($_FILES['file-upload-announcement'])) {
    $target_dir_announcement = "Announcements/";
    $target_file_announcement = $target_dir_announcement . basename($_FILES["file-upload-announcement"]["name"]);
    $uploadOk_announcement = 1;
    $imageFileType_announcement = strtolower(pathinfo($target_file_announcement,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check_announcement = getimagesize($_FILES["file-upload-announcement"]["tmp_name"]);
        if($check_announcement !== false) {
            echo "File is an image - " . $check_announcement["mime"] . ".";
            $uploadOk_announcement = 1;
        } else {
            echo "File is not an image.";
            $uploadOk_announcement = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file_announcement)) {
        echo "Sorry, file already exists.";
        $uploadOk_announcement = 0;
    }
    // Check file size
    if ($_FILES["file-upload-announcement"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk_announcement = 0;
    }
    // Allow certain file formats
    if($imageFileType_announcement != "jpg" && $imageFileType_announcement != "png" && $imageFileType_announcement != "jpeg"
    && $imageFileType_announcement != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk_announcement = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk_announcement == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["file-upload-announcement"]["tmp_name"], $target_file_announcement)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["file-upload-announcement"]["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
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
            <div class="navigation-item" onclick="window.location.href='admin.php'">Logout</div>
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

    <script>
    var slides = document.querySelectorAll('.announcement-carousel .slide');
var currentIndex = 0;
var intervalId; // Variable to hold the interval ID

function startCarousel() {
    intervalId = setInterval(function() {
        changeSlide(1);
    }, 5000); // 5000 milliseconds = 5 seconds
}

function stopCarousel() {
    clearInterval(intervalId);
}

function changeSlide(direction) {
    currentIndex = (currentIndex + direction + slides.length) % slides.length;
    updateSlide();
}

function updateSlide() {
    slides.forEach(function(slide, index) {
        if (index === currentIndex) {
            slide.style.display = 'block';
        } else {
            slide.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updateSlide();
    startCarousel(); // Start the carousel when the page loads
});

// Pause carousel on mouseover
document.querySelector('.announcement-carousel').addEventListener('mouseover', function() {
    stopCarousel();
});

// Resume carousel on mouseout
document.querySelector('.announcement-carousel').addEventListener('mouseout', function() {
    startCarousel();
});

    function openPopup(page) {
        window.open(page + '.php', '_blank', 'width=600,height=400');
    }

    function toggleUploadPopup() {
        var uploadPopup = document.getElementById('upload-popup');
        if (uploadPopup.style.display === 'none') {
            uploadPopup.style.display = 'block';
        } else {
            uploadPopup.style.display = 'none';
        }
    }

    function openGallery(type) {
        var galleryURL = '';
        if (type === 'announcement') {
            galleryURL = 'announcement_gallery.php';
        } else if (type === 'poster') {
            galleryURL = 'poster_gallery.php';
        }
        window.open(galleryURL, '_blank');
    }

    function previewFile(inputId) {
        var fileInput = document.getElementById(inputId);
        var file = fileInput.files[0];
        var fileDetailsSpan = document.getElementById(inputId === 'file-upload-poster' ? 'poster-file-details' : 'announcement-file-details');

        if (file) {
            fileDetailsSpan.textContent = "File name: " + file.name + ", File type: " + file.type;
            fileDetailsSpan.classList.remove('hidden');
        } else {
            fileDetailsSpan.textContent = "";
            fileDetailsSpan.classList.add('hidden');
        }
    }
</script>

</body>
</html>
