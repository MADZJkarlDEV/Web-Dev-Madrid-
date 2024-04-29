<?php
// Include database connection
require_once "config.php";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the delete image button is clicked
    if (isset($_POST["delete_image"])) {
        $image_name = $_POST["delete_image"];
        $announcement_folder = 'Announcements/';
        $image_path = $announcement_folder . $image_name;
        
        // Check if the image exists and delete it from the server
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Check if form data is submitted
    if (isset($_POST["selected_images"]) && is_array($_POST["selected_images"])) {
        // Loop through selected images and insert them into the database
        foreach ($_POST["selected_images"] as $image_name) {
            // Insert the image name into the database if it doesn't already exist
            $stmt_insert_image = $conn->prepare("INSERT INTO selected_images (image_name) SELECT ? WHERE NOT EXISTS (SELECT * FROM selected_images WHERE image_name = ?)");
            $stmt_insert_image->bind_param("ss", $image_name, $image_name);
            $stmt_insert_image->execute();
            $stmt_insert_image->close();
        }
    }
    
    // Remove images from the database if they're unchecked
    if (isset($_POST["remove_image"])) {
        $image_name = $_POST["remove_image"];
        // Remove the image from the database
        $stmt_remove_image = $conn->prepare("DELETE FROM selected_images WHERE image_name = ?");
        $stmt_remove_image->bind_param("s", $image_name);
        $stmt_remove_image->execute();
        $stmt_remove_image->close();
    }

    // Redirect to announcement gallery page
    header("Location: announcement_gallery.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement Gallery</title>
    <style>
        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .gallery table {
            border-collapse: collapse;
            margin: 10px;
        }
        .gallery td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        .gallery img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <h2>Announcement Gallery</h2>
    <form method="post">
        <div class="gallery">
            <?php
            $announcement_folder = 'Announcements/';
            $announcement_images = glob($announcement_folder . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
            // Get currently selected images from the database
            $stmt_select_images = $conn->prepare("SELECT image_name FROM selected_images");
            $stmt_select_images->execute();
            $result = $stmt_select_images->get_result();
            $selected_images = [];
            while ($row = $result->fetch_assoc()) {
                $selected_images[] = $row['image_name'];
            }
            $stmt_select_images->close();
            foreach ($announcement_images as $image):
            ?>
                <table>
                    <tr>
                        <td><img src="<?php echo $image; ?>" alt="Announcement Image"></td>
                        <td><input type="checkbox" name="selected_images[]" value="<?php echo basename($image); ?>" <?php if (in_array(basename($image), $selected_images)) echo "checked"; ?>></td>
                        <td>
                            <button type="submit" name="delete_image" value="<?php echo basename($image); ?>">Delete from Gallery</button>
                            <button type="submit" name="remove_image" value="<?php echo basename($image); ?>">Remove from Database</button>
                        </td>
                    </tr>
                </table>
            <?php endforeach; ?>
        </div>
        <button type="submit">Save Selected Images</button>
    </form>
</body>
</html>
