<?php
session_start();
require 'dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $studentID = $_SESSION['studentID'];
    $message = $_POST['message'];
    $teacherID = $_SESSION['sTeacherID'];
    $owner = 0; // Assuming 1 indicates teacher messages
    $isNew = 1;

    // Get current date and time
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert the message
    $sql = "INSERT INTO messagestudentteacher (teacherID,studentID, message, date, time, owner,isNew) VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $db_connection->prepare($sql)) {
        $stmt->bind_param("iisssii", $teacherID,$studentID, $message, $date, $time, $owner, $isNew);

        if ($stmt->execute()) {
            echo "<script> window.location.href='stdChatBox.php';</script>";
        } else {
            echo "<script>alert('Error sending message. Please try again.'); window.location.href='$urlEdit';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Database error.'); window.location.href='stdChatBox.php';</script>";
    }

    $db_connection->close();
}
?>
