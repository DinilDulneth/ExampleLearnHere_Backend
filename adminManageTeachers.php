<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        table, td {
            border: 1px solid #ccc;
        }

        td {
            font-size: 13px;
            padding: 1px;
            text-align: center;
        }
        th {
            padding: 8px;
            text-align: center;
            background-color: #f2f2f2;
            border: 1px solid #ccc;
        }
        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            padding: 5px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .form-container {
            margin-top: 2px;
        }
        button{
            margin-top: 3px;
        }
        .fa-comments{
            text-decoration: none;
            font-size: 23px;
            color: #4CAF50;
            margin-left: 90px;
            padding: 5px;
        }
    </style>
</head>
<body>

<!-- for page security -->
<?php
        require 'dbConfig.php';
        session_start(); // Start the session at the very top
        
        // Prevent caching
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Validate session variable
        if (!isset($_SESSION['adminID'])) {
            echo "<script> 
                    alert('Unauthorized access. Please log in.');
                    window.location.href = 'adminLogin.php'; 
                </script>";
            exit;
        }

        // Set timeout duration (24 hours)
        $timeout_duration = 24 * 60 * 60; // 24 hours in seconds

        // Check if session has expired
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
            // Session expired
            session_unset();     // Unset session variables
            session_destroy();   // Destroy the session
            echo "<script>
                    alert('Your session has expired due to inactivity. Please log in again.');
                    window.location.href = 'adminLogin.php'; // Redirect to login page
                </script>";
            exit;
        }

        // Update the last activity timestamp
        $_SESSION['last_activity'] = time(); 
        ?>

        <script>
            // Reload the page if it is being loaded from cache
            window.onpageshow = function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            };
        </script>

<!-- page security -->

<?php
            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\Exception;

            require 'vendor/autoload.php'; 

// Activate or Deactivate Teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacherID = $_POST['teacherID'];
    $action = $_POST['action']; // Expected values: "activate" or "deactivate"
    $permission = ($action === "activate") ? 1 : 0;

    $sql = "UPDATE teacher SET permission = ? WHERE teacherID = ?";
    $stmt = $db_connection->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $permission, $teacherID);
        if ($stmt->execute()) {
            $message = "Teacher account successfully " . ($permission ? "activated." : "deactivated.");

            $mail = new PHPMailer(true);
            
            $sql = "SELECT * FROM teacher WHERE teacherID = ?";
            $stmt = $db_connection->prepare($sql);
            $stmt->bind_param("i", $teacherID); // "i" for integer type
            $stmt->execute();
            $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $efName = $row['fName'];
                        $elName = $row['lName'];
                        $eemail = $row['email'];
                        $esubject = $row['subject'];
                        $etelNo = $row['telNo'];
                        $eschool = $row['school'];

                if($action === "activate"){
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
                                    <h1>Welcome to LearnHere</h1>
                                </div>
                                <div class='content'>
                                    <h2>Dear $efName $elName,</h2>
                                    <p>Congratulations! Your teacher account for <strong>LearnHere</strong> has been successfully activated.</p>
                                    <p>We are excited to have you join our platform and contribute to the education of many students.</p>
                                    <p><strong>Your Profile Details:</strong></p>
                                    <p><strong>Email:</strong> $eemail</p>
                                    <p>If you have any questions or need assistance, feel free to contact us anytime.</p>
                                    <p>We look forward to seeing your impactful lessons on LearnHere!</p>
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
                        echo "<script>alert('Welcome email sent to teacher successfully.');</script>";
                    } catch (Exception $e) {
                        // Error handling
                        echo "<script>alert('Failed to send email. Error: " . htmlspecialchars($mail->ErrorInfo) . "');</script>";
                    }

                    
                }else if ($action === "deactivate"){

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
                            <title>Account Deactivation Notice</title>
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
                                    <h1>Account Deactivation Notice</h1>
                                </div>
                                <div class='content'>
                                    <h2>Dear $efName $elName,</h2>
                                    <p>We regret to inform you that your teacher account on <strong>LearnHere</strong> has been deactivated.</p>
                                    <p>This may be due to non-compliance with our guidelines or other administrative reasons.</p>
                                    <p><strong>Your Profile Details:</strong></p>
                                    <p><strong>Email:</strong> $eemail</p>
                                    <p>If you believe this was a mistake or have questions, please contact our support team at <a href='mailto:support@learnhere.com'>support@learnhere.com</a>.</p>
                                    <p>Thank you for your understanding.</p>
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
                        echo "<script>alert('Deactivation email sent to teacher successfully.');</script>";
                    } catch (Exception $e) {
                        // Error handling
                        echo "<script>alert('Failed to send deactivation email. Error: " . htmlspecialchars($mail->ErrorInfo) . "');</script>";
                    }
                    

                }
            }
        }


        } else {
            $error = "Error updating teacher account.";
        }
        $stmt->close();
    } else {
        $error = "Error preparing the update statement.";
    }
}

    // Fetch Teachers Data
    $sql = "SELECT * FROM teacher";
    $result = $db_connection->query($sql);
?>

    <h1>Manage Teachers</h1>

    <!-- Display Success or Error Message -->
    <!-- <?php if (isset($message)) { ?>
        <p class="success"><?php echo htmlspecialchars($message); ?></p>
    <?php } ?>
    <?php if (isset($error)) { ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?> -->

    <!-- Teachers Table -->
    <table>
        <thead>
            <tr>
                <th>Profile Picture</th>
                <th>Teacher ID</th>
                <th>Name</th>
                <th>Subject</th>
                <th>Email</th>
                <th>Telephone</th>
                <th>Level</th>
                <th>Status</th>
                <th>Access Control</th>
            </tr>
        </thead>
        <tbody>
            <?php

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='" . htmlspecialchars($row['profilePicture']) . "' alt='Profile Picture' style='width: 50px; height: 50px; border-radius: 50%;'><br>";

                    $teacherIDAdmin = $row["teacherID"];
                    $sqlisNewAdmin = "SELECT * 
                                    FROM messageadminteacher m , teacher t
                                    WHERE t.teacherID=m.teacherID AND
                                        t.teacherID = $teacherIDAdmin AND
                                        isNewAdmin = 1;";
                                        
                    $resultIsNew = $db_connection ->query($sqlisNewAdmin);

                    $iconColor = ($resultIsNew->num_rows > 0) ? "color:#FF0000;" : "color: #4CAF50;";

                    $urlEdit = "adminChatWithTeach.php?param1=" . urlencode($row["teacherID"]) . "&param2=" . urlencode($row["fName"]) . "&param3=" . urlencode($row["profilePicture"]);
                    echo "<a href='$urlEdit' title='Chat'>
                        <i class='fa fa-comments' style='$iconColor'></i>
                    </a>";

                    echo "</td>";

                    echo "<td>" . htmlspecialchars($row['teacherID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['fName']) . " " . htmlspecialchars($row['lName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    $teacherEmail = htmlspecialchars($row['email']); 
                    echo "<td>" . htmlspecialchars($row['telNo']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['level']) . "</td>";
                    
                    echo "<td>" . ($row['permission'] ? "Active" : "Inactive") . "</td>";
                    echo "<td>
                        <div class='form-container'>
                            <form method='POST' action=''>
                                <input type='hidden' name='teacherID' value='" . htmlspecialchars($row['teacherID']) . "'>";

                    if (htmlspecialchars($row['permission']) == 1) {

                        echo "
                        <label for='action'></label><br>
                                <select name='action' required>";
                                
                        echo "<option value='deactivate'>Deactivate</option>";
                        echo "      </select><br>";
                        echo " <button type='submit'>Deactivate</button>";
                    } else {
                        echo "
                        <label for='action'></label><br>
                                <select name='action' required>";
                                
                        echo "<option value='activate'>Activate</option>";
                        echo "      </select><br>";
                        echo " <button type='submit'>Activate</button>";
                    }
                    echo"
                            </form>
                        </div>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No teachers found.</td></tr>";
            }
            ?>
            
        </tbody>
    </table>
    <div><a href="adminAddTeacher.php">Add New Teacher</div>
</body>
</html>
