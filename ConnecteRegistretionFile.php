<?php
    session_start();
    require 'dbConfig.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer
    

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $telNo = $_POST['telNo'];
    $date = date("Y-m-d"); 
    $address = $_POST['address'];
    $school = $_POST['school'];
    $permission=1;
    $deviceFingerprint = $_POST['deviceFingerprint'];

    // File upload handling
    $profilePicture = null;
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
        $fileTmpPath = $_FILES['profilePicture']['tmp_name'];
        $fileName = $_FILES['profilePicture']['name'];
        $fileSize = $_FILES['profilePicture']['size'];
        $fileType = $_FILES['profilePicture']['type'];
        
        // Allowed file types (you can adjust this)
        $allowedTypes = array("image/jpeg", "image/png", "image/jpg");

        if (in_array($fileType, $allowedTypes)) {
            // Define upload directory
            $uploadDir = 'Upload/studentProfilePicture/';
            // Sanitize file name to prevent issues with special characters
            $fileName = basename($fileName);
            $filePath = $uploadDir . $fileName;

            // Check for file size (e.g., maximum 5MB)
            if ($fileSize > 2000000) {
                echo "File size is too large. Maximum allowed size is 2MB.";
            } else {
                // Move the file to the uploads directory
                if (move_uploaded_file($fileTmpPath, $filePath)) {
                    $profilePicture = $filePath; // Store file path
                } else {
                    echo "Error uploading profile picture.";
                }
            }
        } else {
            echo "<script>alert('Invalid image type! Only image files (JPEG, PNG, JPG) are allowed.');</script>";
        }
    }
    //get the level and teacher id by using session
    $teacherID = $_SESSION['stdTeacherID'];
    $subject = $_SESSION['stdSubject'];
    $level = "A/L";

    // Define values to be bound
    $device2 = null;
    $device3 = null;
    $address = $address ? $address : ''; // Default empty string if no address provided
    $school = $school ? $school : ''; // Default empty string if no school provided

    // SQL insert query
    $sql = "INSERT INTO student (teacherID, fName, lName, password, email, telNo, level, date, device1, device2, device3, subject, address, school, profilePicture, permission)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

    $stmt = $db_connection->prepare($sql);
    $stmt->bind_param("ssssssssssssssss",$teacherID, $fName, $lName, $password, $email, $telNo,$level, $date, $deviceFingerprint, $device2, $device3, $subject, $address, $school, $profilePicture,$permission);

    if ($stmt->execute()) {
        echo "<script>alert('Welcome $fName! Registration successful! Login Now');
        window.location.href='LoginRegisterStd.php';
        </script>";


        // Fetch the teacher's email from the database using teacherID
        $sqlTeacher = "SELECT * FROM teacher WHERE teacherID = ?";
        $stmtTeacher = $db_connection->prepare($sqlTeacher);
        $stmtTeacher->bind_param("i", $teacherID);
        $stmtTeacher->execute();
        $resultTeacher = $stmtTeacher->get_result();

        if ($resultTeacher->num_rows > 0) {
            $teacherEmail = $resultTeacher->fetch_assoc()['email'];
            $teacherName = $resultTeacher->fetch_assoc()['fName'];
        } else {
        }

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
                $mail->addAddress($teacherEmail, $teacherName); // Recipient email and name

                $mail->Subject = "New Student Registration";
                $mail->isHTML(true); // Set email format to HTML
                                    $mail->Body = "
                    <!DOCTYPE html>
                        <html>
                        <head>
                            <style>
                                body {
                                    font-family: 'Arial', sans-serif;
                                    background-color: #f4f4f9;
                                    margin: 0;
                                    padding: 0;
                                    color: #333;
                                }
                                .email-container {
                                    max-width: 600px;
                                    margin: 40px auto;
                                    background: #ffffff;
                                    border-radius: 8px;
                                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                                    overflow: hidden;
                                }
                                .email-header {
                                    background-color: #4CAF50;
                                    color: #ffffff;
                                    text-align: center;
                                    padding: 20px;
                                }
                                .email-header img {
                                    max-height: 50px;
                                    margin-bottom: 10px;
                                }
                                .email-header h1 {
                                    font-size: 24px;
                                    margin: 0;
                                }
                                .email-content {
                                    padding: 20px;
                                    line-height: 1.6;
                                }
                                .email-content img {
                                    display: block;
                                    max-width: 120px;
                                    margin: 20px auto;
                                    border-radius: 50%;
                                }
                                .email-content h2 {
                                    color: #4CAF50;
                                    font-size: 20px;
                                    text-align: center;
                                }
                                .email-content p {
                                    margin: 10px 0;
                                    font-size: 16px;
                                }
                                .email-content p strong {
                                    font-weight: bold;
                                }
                                .email-footer {
                                    background-color: #f4f4f9;
                                    text-align: center;
                                    padding: 10px;
                                    font-size: 14px;
                                    color: #777;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='email-container'>
                                <div class='email-header'>
                                    <img src='https://yourdomain.com/Media/Logo/Learn_Here__1_-removebg-preview.png' alt='LearnHere Logo'>
                                    <h1>New Student Registration</h1>
                                </div>
                                <div class='email-content'>
                                    <img src='Upload/studentProfilePicture/Emily-Wong_Creative-Headshots-NYC-4-removebg-preview.png' alt='Student Profile Picture'>
                                    <h2>Welcome a New Member to LearnHere!</h2>
                                    <p><strong>First Name:</strong> $fName</p>
                                    <p><strong>Last Name:</strong> $lName</p>
                                    <p><strong>Email:</strong> $email</p>
                                    <p><strong>Subject:</strong> $subject</p>
                                    <p><strong>Phone:</strong> $telNo</p>
                                    <p><strong>School:</strong> $school</p>
                                    <p>Please review the student's registration details and take necessary actions.<a href='LoginRegisterTec.php'>Click to review</a></p>
                                </div>
                                <div class='email-footer'>
                                    <p>© 2024 LearnHere. All Rights Reserved.</p>
                                </div>
                            </div>
                        </body>
                        </html>
                        ";

            
                // Send the email
                $mail->send();
                echo "";
            } catch (Exception $e) {
                echo "";
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
                $mail->Subject = "Welcome to LearnHere";

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
                            background-color: #f9f9f9;
                            margin: 0;
                            padding: 0;
                        }
                        .header {
                            background-color: #4CAF50;
                            padding: 20px;
                            text-align: center;
                            color: white;
                        }
                        .header img {
                            width: 100px;
                            margin-bottom: 10px;
                        }
                        .header h2 {
                            margin: 0;
                            font-size: 24px;
                        }
                        .content {
                            padding: 20px;
                            background-color: #fff;
                            margin: 20px auto;
                            border-radius: 8px;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                            max-width: 600px;
                        }
                        .content p {
                            margin: 10px 0;
                            font-size: 16px;
                        }
                        .content a {
                            color: #4CAF50;
                            text-decoration: none;
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
                            <p>Welcome to <strong>LearnHere</strong>, your one-stop platform for seamless educational experiences! We are thrilled to have you join our community of learners and educators.</p>
                            <p>With LearnHere, you can:</p>
                            <ul>
                                <li>Access educational materials curated by top educators.</li>
                                <li>Engage in interactive lessons designed to help you excel.</li>
                                <li>Stay connected with instructors and peers for collaborative learning.</li>
                            </ul>
                            <p>Your account is now active, and you can start exploring the platform by logging in at <a href='https://yourdomain.com/login'>https://yourdomain.com/login</a>.</p>
                            <p>If you have any questions or need assistance, feel free to visit our <a href='https://yourdomain.com/help'>Help Center</a> or contact us at <a href='mailto:support@learnhere.com'>support@learnhere.com</a>.</p>
                            <p>We are excited to support you on your educational journey!</p>
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
        echo "<script>alert('Error during registration. Please try again.');</script>
        window.location.href='LoginRegisterStd.php'
        ";
    }

    $stmt->close();
    $db_connection->close();
}
?>
