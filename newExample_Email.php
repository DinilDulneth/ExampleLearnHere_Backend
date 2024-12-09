<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

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
    $mail->setFrom('dinildulneth123@gmail.com', 'LearnHere Notifications'); // Sender email and name
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
?>
