<?php
session_start();
require 'dbConfig.php';

// Get studentID from session
$studentID = $_SESSION['studentID'];

// Fetch student details from database
$sql = "SELECT * FROM student WHERE studentID = ?";
$stmt = $db_connection->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "<script>alert('Student not found. Please try again later.'); window.location.href='LoginRegisterStd.php';</script>";
    exit();
}

// Update student details after form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $telNo = $_POST['telNo'];
    $address = $_POST['address'];

    // Update query
    $updateSql = "UPDATE student SET fName = ?, lName = ?, telNo = ?, address = ? WHERE studentID = ?";
    $stmt = $db_connection->prepare($updateSql);
    $stmt->bind_param("ssssi", $fName, $lName, $telNo, $address, $studentID);

    if ($stmt->execute()) {
        echo "<script>alert('Your profile has been updated successfully!'); 
                window.location.href='stdMyProfile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again later.'); 
                window.location.href='stdEditMyProfile.php';</script>";
    }
}
?>
