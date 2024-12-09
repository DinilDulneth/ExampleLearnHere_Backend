<?php
session_start();
require 'dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $teachID = $_SESSION['teacherID'];
    $message = $_POST['message'];
    $owner = 1; // Assuming 1 indicates teacher messages
    $isNewAdmin = 1;
    // Get current date and time
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert the message
    $sql = "INSERT INTO messageadminteacher (teacherID, message, owner, date, time,isNewAdmin) VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt = $db_connection->prepare($sql)) {
        $stmt->bind_param("isissi", $teachID, $message, $owner, $date, $time,$isNewAdmin);

        if ($stmt->execute()) {
            echo "<script> window.location.href='teachChatWithAdmin.php';</script>";
        } else {
            echo "<script>alert('Error sending message. Please try again.'); window.location.href='teachChatWithAdmin.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Database error.'); window.location.href='teachChatWithAdmin.php';</script>";
    }

    $db_connection->close();
}
?>
