<?php
require 'dbConfig.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Fetch submitted form data
    $lessonID = $_SESSION['lessonID'];
    $contentDescription = $_POST['contentDescription'];

    // File handling for video, content picture, and PDF
    $videoPath = '';
    $contentPicturePath = '';
    $pdfFilePath = '';

    // Handle video upload
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $videoFileName = basename($_FILES['video']['name']);
        $videoPath = 'Upload/LessonContent/video/' . time() . "_" . $videoFileName;
        if (!move_uploaded_file($_FILES['video']['tmp_name'], $videoPath)) {
            echo "<script>alert('Error uploading video file.');</script>";
            exit;
        }
    }

    // Handle content picture upload
    if (isset($_FILES['contentPicture']) && $_FILES['contentPicture']['error'] === UPLOAD_ERR_OK) {
        $contentPictureFileName = basename($_FILES['contentPicture']['name']);
        $contentPicturePath = 'Upload/LessonContent/picture/' . time() . "_" . $contentPictureFileName;
        if (!move_uploaded_file($_FILES['contentPicture']['tmp_name'], $contentPicturePath)) {
            echo "<script>alert('Error uploading content picture.');</script>";
            exit;
        }
    }

    // Handle PDF file upload
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
        $pdfFileName = basename($_FILES['pdfFile']['name']);
        $pdfFilePath = 'Upload/LessonContent/pdfFile/' . time() . "_" . $pdfFileName;
        if (!move_uploaded_file($_FILES['pdfFile']['tmp_name'], $pdfFilePath)) {
            echo "<script>alert('Error uploading PDF file.');</script>";
            exit;
        }
    }

    // SQL query to insert the content into the database
    $sql = "INSERT INTO lesson_content (lessonID, video, contentPicture, contentDescription, pdfFile) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $db_connection->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("issss", $lessonID, $videoPath, $contentPicturePath, $contentDescription, $pdfFilePath);

        if ($stmt->execute()) {
            echo "<script>alert('Content added successfully!'); 
            window.location.href='teachLessonContentView.php?param1=" . urlencode($_SESSION['lessonName']) . "&param2=" . urlencode($_SESSION["teacherID"]) . "&param3=" . urlencode($_SESSION["lessonID"]) . "';</script>";
        } else {
            echo "<script>alert('Error adding content: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error: Unable to prepare the statement.');</script>";
    }
}
?>
