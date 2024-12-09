<?php
session_start();
require 'dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $teacherID = $_POST['teacherID'];
    

    $teacherName = $_POST['teacherName'];
    $proPic = $_POST['profilePic'];

    $message = $_POST['message'];
    $owner = 0; 
    $isNew=1;

    // Get current date and time
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert the message
    $sql = "INSERT INTO messageadminteacher (teacherID,  message, date, time, owner,isNew) VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt = $db_connection->prepare($sql)) {
        $stmt->bind_param("isssii",$teacherID, $message, $date, $time, $owner,$isNew);

        if ($stmt->execute()) {
            $urlEdit = "adminChatWithTeach.php?param1=" . urlencode($teacherID) . "&param2=" . urlencode($teacherName) . "&param3=" . urlencode($proPic) ;
            echo "<script> 
            window.location.href='$urlEdit';</script>";
        } else {
            $urlEdit = "adminChatWithTeach.php?param1=" . urlencode($teacherID) . "&param2=" . urlencode($teacherName) . "&param3=" . urlencode($proPic) ;
            echo "<script>alert('Error sending message. Please try again.'); 
            window.location.href='$urlEdit';</script>";
        }

        $stmt->close();
    } else {

    }

    $db_connection->close();
}
?>
