<?php
session_start();
require 'dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $teachID = $_SESSION['teacherID'];
    $message = $_POST['message'];
    $studentID = $_POST['studentID'];
    $profilePicture = $_POST['profilePicture'];
    $fName = $_POST['fName'];
    $owner = 1; // Assuming 1 indicates teacher messages
    $isNewStd = 1;
    // Get current date and time
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert the message
    $sql = "INSERT INTO messagestudentteacher (teacherID,studentID, message, date, time, owner,isNewStd) VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $db_connection->prepare($sql)) {
        $stmt->bind_param("iisssii", $teachID,$studentID, $message, $date, $time, $owner, $isNewStd);

        $urlEdit = "teachChatWithStd.php?param1=" . urlencode($studentID) . "&param2=" . urlencode($fName) . "&param3=" . urlencode($profilePicture) ;

        if ($stmt->execute()) {
            echo "<script> window.location.href='$urlEdit';</script>";
        } else {
            echo "<script>alert('Error sending message. Please try again.'); window.location.href='$urlEdit';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Database error.'); window.location.href='$urlEdit';</script>";
    }

    $db_connection->close();
}
?>
