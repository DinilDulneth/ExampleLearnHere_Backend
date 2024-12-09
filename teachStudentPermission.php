<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'dbConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = isset($_POST['studentID']) ? intval($_POST['studentID']) : 0;
    $permission = isset($_POST['permission']) ? intval($_POST['permission']) : null;

    // Validate input
    if ($studentID === 0 || $permission === null) {
        echo "<script>
                alert('Invalid input. Please try again!');
                window.location.href = 'teachStudents.php';
              </script>";
        exit;
    }

    // Get student details for the email
    $sql = "SELECT * FROM student WHERE studentID = ?";
    $stmt = $db_connection->prepare($sql);
    $stmt->bind_param('i', $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch student data
        $student = $result->fetch_assoc();
        $efName = $student['fName'];
        $elName = $student['lName'];
        $eemail = $student['email'];
    } else {
        echo "<script>
                alert('Student not found!');
                window.location.href = 'teachStudents.php';
              </script>";
        exit;
    }

    // Update query
    $sql = "UPDATE student SET permission = ? WHERE studentID = ?";
    $stmt = $db_connection->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('ii', $permission, $studentID);

        if ($stmt->execute()) {
            if ($permission === 1) {
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
                        //$mail->addAddress($eemail, $efName . ' ' . $elName); // Recipient's email and name

                        $mail->Subject = "Welcome to LearnHere - Your Account is Activated";
                        $mail->isHTML(true); // Set email format to HTML
                        $mail->Body = "
                        <html>
                    <head>
                        <title>Account Activation</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                line-height: 1.6;
                                color: #333;
                                background-color: #f9f9f9;
                                margin: 0;
                                padding: 0;
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
                                <img src='https://yourwebsite.com/Media/Logo/Learn_Here__1_-removebg-preview.png' alt='LearnHere Logo'>
                                <h1>Account Activation</h1>
                            </div>
                            <div class='content'>
                                <h2>Dear $efName $elName,</h2>
                                <p>Congratulations! Your teacher account for <strong>LearnHere</strong> has been successfully activated.</p>
                                <p>You can now access the system and start contributing your lessons to students. We are excited to have you on board!</p>
                                <p><strong>Your Profile Details:</strong></p>
                                <p><strong>Email:</strong> $eemail</p>
                                <p>If you have any questions or need assistance, feel free to contact us anytime.</p>
                                <p>Thank you for joining LearnHere, and we look forward to your contributions!</p>
                                <p>Best regards,</p>
                                <p><strong>The LearnHere Team</strong></p>
                            </div>
                            <div class='footer'>
                                <p>&copy; " . date('Y') . " LearnHere. All rights reserved.</p>
                            </div>
                        </div>
                    </body>
                    </html>";
                
                    // Send the email
                    $mail->send();
                    //echo "<script>alert('Activation email sent to Student successfully.');</script>";
                } catch (Exception $e) {
                    // Error handling
                    echo "<script>alert('Failed to send activation email. Error: " . htmlspecialchars($mail->ErrorInfo) . "');</script>";
                }

                echo "<script>
                        alert('Permission granted to the system!');
                        window.location.href = 'teachStudents.php';
                      </script>";
            
                      
            } else {
                
                $mail = new PHPMailer(true);
                      try {
                        // Enable verbose debug output (for troubleshooting, can be disabled later)
                        $mail->SMTPDebug = 0; // Use PHPMailer::DEBUG_SERVER for detailed debug logs
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'dinildulneth123@gmail.com'; // Your Gmail address
                        $mail->Password = 'uvvo udcp wmap acqm'; // Gmail App Password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use PHPMailer::ENCRYPTION_STARTTLS for port 587
                        $mail->Port = 465; // Use 587 for STARTTLS
                    
                        // Email details
                        $mail->setFrom('dinildulneth123@gmail.com', 'LearnHere'); // Sender email and name
                        $mail->addAddress('digitalartdinil@gmail.com', 'Dinil Digital Art'); // Recipient email and name
                        //$mail->addAddress($eemail, $efName . ' ' . $elName); // Recipient's email and name
                    
                        $mail->Subject = "Account Deactivation Notice - LearnHere";
                        $mail->isHTML(true); // Set email format to HTML
                        $mail->Body = "
                        <html>
                    <head>
                        <title>Account Deactivation</title>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 0; }
                            .email-container { background-color: #ffffff; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-width: 600px; }
                            .header { text-align: center; margin-bottom: 20px; }
                            .header img { width: 150px; }
                            .content { margin-top: 10px; }
                            .content h2 { color: #555; }
                            .content p { margin: 10px 0; }
                            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #999; }
                        </style>
                    </head>
                    <body>
                        <div class='email-container'>
                            <div class='header'>
                                <img src='https://yourwebsite.com/Media/Logo/Learn_Here__1_-removebg-preview.png' alt='LearnHere Logo'>
                                <h1>Account Deactivation</h1>
                            </div>
                            <div class='content'>
                                <h2>Dear $efName $elName,</h2>
                                <p>We regret to inform you that your teacher account for <strong>LearnHere</strong> has been deactivated.</p>
                                <p>If you believe this is an error or if you would like to re-enable your account, please contact our support team for assistance.</p>
                                <p><strong>Your Profile Details:</strong></p>
                                <p><strong>Email:</strong> $eemail</p>
                                <p>We hope to have you back soon.</p>
                                <p>Best regards,</p>
                                <p><strong>The LearnHere Team</strong></p>
                            </div>
                            <div class='footer'>
                                <p>&copy; " . date('Y') . " LearnHere. All rights reserved.</p>
                            </div>
                        </div>
                    </body>
                    </html>";

                    $mail->send();
                    //echo "<script>alert('Deactivation email sent to teacher successfully.');</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Failed to send deactivation email. Error: " . htmlspecialchars($mail->ErrorInfo) . "');</script>";
                }
                echo "<script>
                        alert('Permission revoked from the system!');
                        window.location.href = 'teachStudents.php';
                      </script>";

            }
        } else {
            echo "<script>
                    alert('Error updating permission. Please try again!');
                    window.location.href = 'teachStudents.php';
                  </script>";
        }
        $stmt->close();
    } else {
        echo "<script>
                alert('Database error: Unable to prepare the statement. Please try again!');
                window.location.href = 'teachStudents.php';
              </script>";
    }
}
?>
