<?php
session_start();  // Start the session to get the teacherID from the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $price = $_POST['price'];
    $name = $_POST['name'];
    $date = date("Y-m-d");  
    $teacherID = $_SESSION['teacherID'];  
    $description = $_POST['description'];

    $thumbnailPicture = null;  // Initialize thumbnail as null (not required)
    if (isset($_FILES['thumbnailPicture']) && $_FILES['thumbnailPicture']['error'] == 0) {
        $fileTmpPath = $_FILES['thumbnailPicture']['tmp_name'];
        $fileName = $_FILES['thumbnailPicture']['name'];
        $fileSize = $_FILES['thumbnailPicture']['size'];
        $fileType = $_FILES['thumbnailPicture']['type'];
        
        // Allowed file types (you can adjust this)
        $allowedTypes = array("image/jpeg", "image/png", "image/jpg");

        // Use getimagesize() to confirm the image is valid (it checks file content, not just extension)
        $imageInfo = getimagesize($fileTmpPath);
        if ($imageInfo !== false) {
            // Image is valid
            $mime = $imageInfo['mime'];
            if (in_array($mime, $allowedTypes)) {
                // Define upload directory
                $uploadDir = 'Upload/lessonThumbnailPic/';
                // Sanitize file name to prevent issues with special characters
                $fileName = basename($fileName);
                $filePath = $uploadDir . $fileName;

                // Check for file size (e.g., maximum 2MB)
                if ($fileSize > 2000000) {
                    echo "<script>alert('File size is too large. Maximum allowed size is 2MB.');</script>";
                } else {
                    // Move the file to the uploads directory
                    if (move_uploaded_file($fileTmpPath, $filePath)) {
                        $thumbnailPicture = $filePath; // Store file path
                    } else {
                        echo "<script>alert('Error uploading the thumbnail image.');</script>";
                    }
                }
            } else {
                echo "<script>alert('Invalid image type! Only image files (JPEG, PNG, JPG) are allowed.');</script>";
            }
        } else {
            echo "<script>alert('File is not a valid image.');</script>";
        }
    }

    // Check if $thumbnailPicture has a valid value
    if ($thumbnailPicture) {
        echo "<script>alert('Thumbnail uploaded successfully: $thumbnailPicture');</script>";
    } else {
        echo "<script>alert('No thumbnail uploaded.');</script>";
    }

    // Database connection (make sure you have dbConfig.php or your DB credentials)
    require 'dbConfig.php';

    // Insert the form data into the database
    $sql = "INSERT INTO lesson (price, name, date, teacherID, description, thumbnailPicture) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare the SQL statement
    if ($stmt = $db_connection->prepare($sql)) {
        $stmt->bind_param("ssssss", $price, $name, $date, $teacherID, $description, $thumbnailPicture);

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: teachLessonsPage.php"); 
            echo "<script>alert('Lesson added successfully!');</script>";
        } else {
            echo "<script>alert('Error, try again!');</script>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing the SQL statement.');</script>";
    }

    // Close the database connection
    $db_connection->close();
}
?>
