<?php

require 'dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['lemail'];
    $psw = $_POST['lpassword'];
    $deviceFingerprint = $_POST['deviceFingerprintlog'];

    $sql = "SELECT * 
            FROM teacher 
            WHERE email = '$email' 
                AND password = '$psw'";

    $result = $db_connection->query($sql);

    // Get teacher details
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $TeachName = $row["fName"];
            $teacherID = $row["teacherID"];
            $TeachProfilePic = $row["profilePicture"];
            $permission = $row["permission"]; // Check the permission value
        }

        // Check if teacher has permission (permission = 1 means active, 0 means inactive)
        if ($permission == 0) {
            echo "<script>alert('Your account is not active. Please wait for administrator approval.'); 
                  window.location.href='LoginRegisterTec.php';</script>";
            exit; // Stop further execution
        }

        // Teacher has permission, start session
        session_start(); // Start the session

        // Storing data in the session
        $_SESSION['teacherName'] = $TeachName;
        $_SESSION['teacherProfilePic'] = $TeachProfilePic;
        $_SESSION['teacherID'] = $teacherID;
    }

    // Check teacher login and permission
    if ($result->num_rows == 1 && $permission == 1) {
        // Redirect to teacher dashboard
        header("Location: teacherDashboard.php");

        // Record the student login
        $loginDate = date("Y-m-d");
        $time = date("H:i:s");
        $deviceInfo = $_SERVER['HTTP_USER_AGENT'];

        $sql = "INSERT INTO teacherloginhistory (teacherID, loginDate, time, deviceFingerprint, deviceInfo) 
                VALUES (?, ?, ?, ?, ?);";

        $stmt = $db_connection->prepare($sql);
        $stmt->bind_param("sssss", $teacherID, $loginDate, $time, $deviceFingerprint, $deviceInfo);

        if ($stmt->execute()) {
            echo "<script>console.log('Login history inserted')</script>";
        } else {
            echo "Error: ";
        }
    } else {
        echo "<script>alert('Email and password do not match. Please try again!'); 
              window.location.href='LoginRegisterTec.php';</script>";
    }
}
?>
