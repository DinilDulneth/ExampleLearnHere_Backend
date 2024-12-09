<?php
// Include your database connection file
require 'dbConfig.php';
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $lessonID = $_POST['lessonID'];
    $studentID = $_POST['studentID'];
    $teacherID = $_POST['teacherID'];
    $expireDate = $_POST['expireDate']; // This is the date entered by the user 
    $paymentID = $_POST['paymentID'];
    
    // Get the current date as the start date
    $startDate = date('Y-m-d'); // Current date in 'YYYY-MM-DD' format
    
    // Check if the record already exists in access_table
    $checkSql = "SELECT * FROM access_table WHERE studentID = ? AND teacherID = ? AND lessonID = ?";
    
    if ($stmt = $db_connection->prepare($checkSql)) {
        // Bind parameters to the query
        $stmt->bind_param("iii", $studentID, $teacherID, $lessonID);

        // Execute the query
        $stmt->execute();
        $stmt->store_result();


        if ($stmt->num_rows > 0) {
            // Record exists, update expireDate
            $updateSql = "UPDATE access_table SET expireDate = ? WHERE studentID = ? AND teacherID = ? AND lessonID = ?";
            
            if ($updateStmt = $db_connection->prepare($updateSql)) {
                // Bind parameters to the query
                $updateStmt->bind_param("siii", $expireDate, $studentID, $teacherID, $lessonID);

                // Execute the update query
                if ($updateStmt->execute()) {
                    echo "<script>alert('Expire date updated successfully.'); 
                    window.location.href='teachStdPayments.php';</script>";

                    
                        // Update isNew to 0 after showing the payment
                        $updateSql = "UPDATE payment SET isNew = 0 WHERE paymentID = ?";
                        $updateStmt = $db_connection->prepare($updateSql);
                        $updateStmt->bind_param("i", $paymentID);
                        $updateStmt->execute();

                        // if ($updateStmt->affected_rows > 0) {
                        //     echo "<p>Payment ID " . $row['paymentID'] . " updated successfully to 'isNew = 0'.</p>";
                        // } else {
                        //     echo "<p>Failed to update Payment ID " . $row['paymentID'] . ".</p>";
                        // }

                        $updateStmt->close();

                } else {
                    echo "<script>alert('Error updating expire date, please try again!'); 
                    window.location.href='teachStdPayments.php';</script>";
                }

                // Close the update statement
                $updateStmt->close();
            }
        } else {
            // Record doesn't exist, insert a new record
            $insertSql = "INSERT INTO access_table (studentID, teacherID, lessonID, startDate, expireDate) 
                          VALUES (?, ?, ?, ?, ?)";

            if ($insertStmt = $db_connection->prepare($insertSql)) {
                // Bind parameters to the query
                $insertStmt->bind_param("iiiss", $studentID, $teacherID, $lessonID, $startDate, $expireDate);

                // Execute the insert query
                if ($insertStmt->execute()) {
                    echo "<script>alert('Access is successfully granted.'); 
                    window.location.href='teachStdPayments.php';</script>";
                } else {
                    echo "<script>alert('Error granting access, please try again!'); 
                    window.location.href='teachStdPayments.php';</script>";
                }

                // Close the insert statement
                $insertStmt->close();
            }
        }

        // Close the check statement
        $stmt->close();
    } else {
        echo "<script>alert('Database error, unable to check for existing record.'); 
        window.location.href='teachStdPayments.php';</script>";
    }
}

// Close the database connection
$db_connection->close();
?>
