<?php
require "dbConfig.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Fetch submitted form data
    $lessonID = intval($_POST['lessonID']);
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;
    $currentThumbnail = isset($_POST['currentThumbnail']) ? htmlspecialchars($_POST['currentThumbnail']) : '';

    // Thumbnail upload handling
    $thumbnailPath = $currentThumbnail; // Default to the current thumbnail
    if (isset($_FILES['newThumbnail']) && $_FILES['newThumbnail']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = $_FILES['newThumbnail']['type'];

        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = "Upload/lessonThumbnailPic/";

            $fileName = basename($_FILES['newThumbnail']['name']);
            $targetFile = $uploadDir . time() . "_" . $fileName; // Prevent file overwrites

            if (move_uploaded_file($_FILES['newThumbnail']['tmp_name'], $targetFile)) {
                $thumbnailPath = $targetFile;

                // Delete old thumbnail if new one is uploaded
                if (!empty($currentThumbnail)) {
                    // Ensure it's a valid file path and exists before deleting
                    $oldThumbnailPath = $currentThumbnail;

                    if (file_exists($oldThumbnailPath)) {
                        unlink($oldThumbnailPath); // Correct way to delete the file
                    } else {
                        echo "<script>console.log('Old thumbnail not found. Old thumbnail path: " . $oldThumbnailPath . "');</script>";

                    }
                }

            } else {
                echo "<script>
                        alert('Error uploading the thumbnail. Please try again.');
                        window.location.href='teachEditLesson.php';
                      </script>";
                exit;
            }
        } else {
            echo "<script>
                    alert('Invalid thumbnail format. Only JPEG, PNG, and JPG are allowed.');
                    window.location.href='teachEditLesson.php';
                  </script>";
            exit;
        }
    }

    // Check if lesson ID is valid
    if ($lessonID === 0) {
        echo "<script>alert('Invalid Lesson ID.');</script>";
        exit;
    }

    // Update query
    $sql = "UPDATE lesson 
            SET name = ?, description = ?, price = ?, thumbnailPicture = ? 
            WHERE lessonID = ?";
    $stmt = $db_connection->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssdsi", $name, $description, $price, $thumbnailPath, $lessonID);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Lesson details updated successfully!');
                    window.location.href='teachLessonsPage.php';
                  </script>";
        } else {
            echo "<script>alert('Error updating lesson details: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Database error: Unable to prepare the statement.');</script>";
    }
}
?>
