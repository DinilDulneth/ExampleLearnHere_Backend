<?php
require 'dbConfig.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data
    $fName = trim($_POST['fName']);
    $lName = trim($_POST['lName']);
    $subject = trim($_POST['subject']);
    $email = trim($_POST['email']);
    $telNo = trim($_POST['telNo']);
    $password = trim($_POST['password']);
    $level = "A/L";
    $description = trim($_POST['description']);
    $school = trim($_POST['school']);
    $date = date("Y-m-d");
    $permission = 0; // Default permission set to 0 (inactive)

    // File upload handling
    $profilePicture = null;
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
        $fileTmpPath = $_FILES['profilePicture']['tmp_name'];
        $fileName = $_FILES['profilePicture']['name'];
        $fileSize = $_FILES['profilePicture']['size'];
        $fileType = $_FILES['profilePicture']['type'];

        // Allowed file types
        $allowedTypes = array("image/jpeg", "image/png", "image/jpg");

        // Check if the uploaded file type is allowed
        if (in_array($fileType, $allowedTypes)) {
            // Define upload directory
            $uploadDir = 'Upload/teacherProfilePicture/';
            // Sanitize file name
            $fileName = basename($fileName);
            $filePath = $uploadDir . $fileName;

            // Check file size (max 2MB)
            if ($fileSize > 2000000) {
                echo "<script>alert('File size is too large. Maximum allowed size is 2MB.');</script>";
            } else {
                // Move the file to the uploads directory
                if (move_uploaded_file($fileTmpPath, $filePath)) {
                    $profilePicture = $filePath; // Store file path in the database
                } else {
                    echo "<script>alert('Error uploading profile picture.');</script>";
                }
            }
        } else {
            echo "<script>alert('Invalid image type! Only JPEG, PNG, or JPG are allowed.');</script>";
        }
    }

    // Default empty string for school if not provided
    $school = $school ? $school : '';

    // Prepare SQL query to insert data into the Teacher table
    $sql = "INSERT INTO Teacher (fName, lName, password, email, telNo, level, date, subject, profilePicture, description, school, permission)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

    $stmt = $db_connection->prepare($sql);
    if ($stmt === false) {
        die('SQL error: ' . $db_connection->error);
    }

    $stmt->bind_param(
        "sssssssssssi", 
        $fName, 
        $lName, 
        $password, 
        $email, 
        $telNo, 
        $level, 
        $date, 
        $subject, 
        $profilePicture, 
        $description, 
        $school, 
        $permission
    );

    if ($stmt->execute()) {
        // Show success message and redirect
        echo "<script>alert('Welcome Mr./Mrs. $fName! Registration successful. Please wait for administrator approval.');
            window.location.href='LoginRegisterTec.php';
            </script>";
            
            $mail = new PHPMailer(true);
            
            try {
                // Enable verbose debug output (for troubleshooting, can be disabled later)
                $mail->SMTPDebug = 0; // Set to PHPMailer::DEBUG_SERVER for detailed debug logs
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'dinildulneth123@gmail.com'; // Your Gmail address
                $mail->Password = 'uvvo udcp wmap acqm'; // Your Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use PHPMailer::ENCRYPTION_STARTTLS for port 587
                $mail->Port = 465; // Use 587 if you're using STARTTLS
            
                // Email details
                $mail->setFrom('dinildulneth123@gmail.com', 'LearnHere'); // Sender email and name
                $mail->addAddress('digitalartdinil@gmail.com', 'Dinil Digital Art'); // Recipient email and name
            
                $mail->Subject = "New Teacher Registration";
                $mail->isHTML(true); // Set email format to HTML
                                    $mail->Body = "
                    <html>
                    <head>
                        <title>New Teacher Registration Notification</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                line-height: 1.6;
                                color: #333;
                                background-color: #f9f9f9;
                            }
                            .email-container {
                                background-color: #ffffff;
                                margin: 20px auto;
                                padding: 20px;
                                border: 1px solid #ddd;
                                border-radius: 8px;
                                max-width: 600px;
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 20px;
                            }
                            .header img {
                                width: 150px;
                            }
                            .teacher-image {
                                text-align: center;
                                margin: 20px 0;
                            }
                            .teacher-image img {
                                width: 120px;
                                height: 120px;
                                border-radius: 50%;
                                border: 2px solid #ddd;
                            }
                            .content {
                                margin-top: 10px;
                            }
                            .content h2 {
                                color: #555;
                            }
                            .content p {
                                margin: 10px 0;
                            }
                            .footer {
                                text-align: center;
                                margin-top: 20px;
                                font-size: 12px;
                                color: #999;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='email-container'>
                            <div class='header'>
                                <img src='Media/Logo/Learn_Here__1_-removebg-preview.png' alt='LearnHere Logo'> 

                                <!-- Replace with actual logo URL = <img src='https://yourdomain.com/Media/Logo/Learn_Here__1_-removebg-preview.png' alt='LearnHere Logo'>-->
                                
                                <h1>New Teacher Registration</h1>
                            </div>
                            <div class='teacher-image'>
                                <img src='Upload/teacherProfilePicture/Emily-Wong_Creative-Headshots-NYC-4-removebg-preview.png' alt='Teacher Profile Picture'> 
                                
                                <!-- Replace with actual profile picture path = https://yourdomain.com/$' -->
                            
                            </div>
                            <div class='content'>
                                <h2>A new teacher has registered for LearnHere!</h2>
                                <p><strong>First Name:</strong> $fName</p>
                                <p><strong>Last Name:</strong> $lName</p>
                                <p><strong>Email:</strong> $email</p>
                                <p><strong>Subject:</strong> $subject</p>
                                <p><strong>Phone:</strong> $telNo</p>
                                <p><strong>School:</strong> $school</p>
                                <p>Please review and approve the registration.</p>
                            </div>
                            <div class='footer'>
                                <p>&copy; " . date('Y') . " LearnHere. All rights reserved.</p>
                            </div>
                        </div>
                    </body>
                    </html>";

            
                // Send the email
                $mail->send();
                echo "<script>alert('Registration notification sent to admin successfully.');</script>";
            } catch (Exception $e) {
                // Error handling
                echo "<script>alert('Failed to send email. Error: {$mail->ErrorInfo}');</script>";
            }


            $mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'dinildulneth123@gmail.com'; // Replace with your Gmail address
    $mail->Password = 'uvvo udcp wmap acqm'; // Replace with your Gmail app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Sender and recipient information
    $mail->setFrom('dinildulneth123@gmail.com', 'LearnHere'); // Sender email and name
    $mail->addAddress($email, "$fName $lName"); // Recipient email and name

    // Email subject
    $mail->Subject = "Welcome to LearnHere - Your Registration is Pending Approval";

    // Email body content
    $mail->isHTML(true);
    $mail->Body = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #333;
                line-height: 1.6;
            }
            .header {
                background-color: #f1f1f1;
                padding: 20px;
                text-align: center;
                border-bottom: 2px solid #4CAF50;
            }
            .header img {
                width: 120px;
            }
            .content {
                padding: 20px;
            }
            .footer {
                margin-top: 20px;
                text-align: center;
                font-size: 12px;
                color: #777;
            }
        </style>
    </head>
    <body>
        <div class='header'>
            <img src='https://yourdomain.com/Media/Logo/Learn_Here__1_-removebg-preview.png' alt='LearnHere Logo'>
            <h2>Welcome to LearnHere!</h2>
        </div>
        <div class='content'>
            <p>Dear $fName $lName,</p>
            <p>Thank you for registering with LearnHere! We are excited to have you join our platform. Our system is designed to help educators and students connect, collaborate, and succeed in their educational journey.</p>
            <p>Your account has been successfully registered but is currently awaiting administrator approval. Once your account is reviewed and approved, you will receive a confirmation email with access to the system's full features.</p>
            <p>In the meantime, feel free to explore our <a href='https://yourdomain.com/faq'>FAQ section</a> for any questions or reach out to us at <a href='mailto:support@learnhere.com'>support@learnhere.com</a> for assistance.</p>
            <p>We look forward to seeing you thrive with LearnHere!</p>
            <p>Best regards,<br>The LearnHere Team</p>
        </div>
        <div class='footer'>
            <p>&copy; " . date("Y") . " LearnHere. All rights reserved.</p>
        </div>
    </body>
    </html>
    ";

    // Send the email
    $mail->send();
    echo "<script>alert('Welcome email sent to user successfully.');</script>";
} catch (Exception $e) {
    echo "<script>alert('Failed to send welcome email. Error: {$mail->ErrorInfo}');</script>";
}


    } else {
        // Error during registration
        echo "<script>alert('Error during registration. Please try again.');</script>";
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $db_connection->close();
}
?>
