<?php
// Include database connection
require 'dbConfig.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

session_start();

// Collect POST data
$lessonID = isset($_POST['lessonID']) ? $_POST['lessonID'] : '';
$teacherID = isset($_POST['teacherID']) ? $_POST['teacherID'] : '';
$studentID = isset($_POST['studentID']) ? $_POST['studentID'] : '';
$lessonPrice = isset($_POST['lessonPrice']) ? $_POST['lessonPrice'] : '';
$date = date('Y-m-d'); // Current date
$time = date('H:i:s'); // Current time

// Validate required fields
if (empty($lessonID) || empty($teacherID) || empty($studentID) || empty($lessonPrice)) {
    echo "Required data is missing.";
    exit();
}

// Handle payment slip upload
$paymentSlip = ''; // Default empty value
if (isset($_FILES['paymentSlip']) && $_FILES['paymentSlip']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'Upload/StdPaymentLesson/'; // Directory for uploaded files
    $fileName = basename($_FILES['paymentSlip']['name']);
    $targetPath = $uploadDir . uniqid() . '_' . $fileName;

    // Ensure the uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['paymentSlip']['tmp_name'], $targetPath)) {
        $paymentSlip = $targetPath; // Store the file path
    } else {
        echo "Failed to upload payment slip.";
        exit();
    }
} else {
    echo "No payment slip uploaded or invalid file.";
    exit();
}

// Insert data into payment table
$sql = "INSERT INTO payment (studentID, teacherID, lessonID, lessonPrice, paymentSlip, date, time, isNew) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $db_connection->prepare($sql);
$isNew = 1; // Mark the payment as new (1 = true)

// Bind parameters
$stmt->bind_param("iiidsssi", $studentID, $teacherID, $lessonID, $lessonPrice, $paymentSlip, $date, $time, $isNew);

// Execute the query
if ($stmt->execute()) {

    if ($stmt->execute()) {
        // Fetch teacher, lesson, and student details
        $sqlFetch = "
            SELECT 
                t.email AS teacherEmail, 
                CONCAT(t.fName, ' ', t.lName) AS teacherName, 
                l.name AS lessonName,
                s.fName AS studentFirstName,
                s.lName AS studentLastName
            FROM 
                teacher t 
            INNER JOIN 
                lesson l ON t.teacherID = l.teacherID 
            INNER JOIN 
                payment p ON l.lessonID = p.lessonID
            INNER JOIN 
                student s ON p.studentID = s.studentID
            WHERE 
                t.teacherID = ? AND l.lessonID = ? AND p.studentID = ?";
        
        $stmtFetch = $db_connection->prepare($sqlFetch);
        $stmtFetch->bind_param("iii", $teacherID, $lessonID, $studentID);
        
        if ($stmtFetch->execute()) {
            $result = $stmtFetch->get_result();
            if ($result->num_rows > 0) {
                $details = $result->fetch_assoc();
                $teacherEmail = $details['teacherEmail'];
                $teacherName = $details['teacherName'];
                $lessonName = $details['lessonName'];
                $studentFName = $details['studentFirstName'];
                $studentLName = $details['studentLastName'];
            } else {
                echo "<script>alert('Details not found.');</script>";
                exit();
            }
        }
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
    $mail->setFrom('dinildulneth123@gmail.com', 'LearnHere Notifications'); // Sender email and name
    //$mail->addAddress('digitalartdinil@gmail.com', 'Dinil Digital Art'); // Recipient email and name
    $mail->addAddress($teacherEmail, $teacherName); // Teacher's email

    $mail->Subject = "New Payment";
    $mail->isHTML(true); // Set email format to HTML
    $mail->Body = "
    <html>
    <h1>LearnHere</h1>
        <body style=\"font-family: Arial, sans-serif; line-height: 1.6; color: #333;\">
            <div style=\"max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;\">
                <div style=\"text-align: center; margin-bottom: 20px;\">
                    <h2 style=\"color: #0056b3;\">Payment Notification</h2>
                </div>
                <p>Dear <strong>$teacherName</strong>,</p>
                <p>We are pleased to inform you that a payment has been made for one of your lessons on <strong>LearnHere</strong>. Please review the payment details below:</p>
                <table style=\"width: 100%; border-collapse: collapse; margin: 20px 0;\">
                    <tr>
                        <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Lesson Name:</td>
                        <td style=\"padding: 10px; border: 1px solid #ddd;\">$lessonName</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Student Name:</td>
                        <td style=\"padding: 10px; border: 1px solid #ddd;\">$studentFName $studentLName</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Payment Amount:</td>
                        <td style=\"padding: 10px; border: 1px solid #ddd;\">$lessonPrice</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Payment Date:</td>
                        <td style=\"padding: 10px; border: 1px solid #ddd;\">$date</td>
                    </tr>
                </table>
                <p>To proceed, please log in to your LearnHere account and take action</p>
                <ul>
                    <li><strong>Accept Payment:</strong> Approve the payment and confirm the lesson.</li>
                    <li><strong>Revoke Payment:</strong> Decline the payment with an appropriate reason.</li>
                </ul>

                <p>If you have any questions, feel free to contact our support team.</p>
                <p>Best regards,</p>
                <p><strong>The LearnHere Team</strong></p>
                <div style=\"margin-top: 20px; font-size: 12px; color: #999; text-align: center;\">
                    <p>&copy; " . date('Y') . " LearnHere. All rights reserved.</p>
                </div>
            </div>
        </body>
    </html>
    ";

    // Send the email
    $mail->send();
    echo "<script>alert('Registration notification sent to admin successfully.');</script>";
} catch (Exception $e) {
    // Error handling
    echo "<script>alert('Failed to send email. Error: {$mail->ErrorInfo}');</script>";
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
    $mail->setFrom('dinildulneth123@gmail.com', 'LearnHere Notifications'); // Sender email and name
    $mail->addAddress('digitalartdinil@gmail.com', 'Dinil Digital Art'); // Recipient email and name

    $mail->Subject = "Payment Notification";
    $mail->isHTML(true); // Set email format to HTML
    $mail->Body = "
    <html>
    <h1>LearnHere</h1>
        <body style=\"font-family: Arial, sans-serif; line-height: 1.6; color: #333;\">
            <div style=\"max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;\">
                <div style=\"text-align: center; margin-bottom: 20px;\">
                    <h2 style=\"color: #0056b3;\">Payment Notification for Your Lesson</h2>
                </div>
                <p>Dear Admin,</p>
                <p>We would like to inform you that a payment has been successfully received for one of the lessons on <strong>LearnHere</strong>. Below are the details of the payment:</p>
                <table style=\"width: 100%; border-collapse: collapse; margin: 20px 0; border: 1px solid #ddd;\">
                    <tr>
                        <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Lesson Name:</td>
                        <td style=\"padding: 10px; border: 1px solid #ddd;\">$lessonName</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Teacher Name:</td>
                        <td style=\"padding: 10px; border: 1px solid #ddd;\">$teacherName</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Student Name:</td>
                        <td style=\"padding: 10px; border: 1px solid #ddd;\">$studentFName $studentLName</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Payment Amount:</td>
                        <td style=\"padding: 10px; border: 1px solid #ddd;\">$lessonPrice</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Payment Date:</td>
                        <td style=\"padding: 10px; border: 1px solid #ddd;\">$date</td>
                    </tr>
                </table>
                <p>No further action is required at this time. This email is simply for your reference and to keep you informed about the latest payments for the lessons.</p>
                <p>Thank you for your continued support of LearnHere.</p>
                <p>Best regards,</p>
                <p><strong>The LearnHere Team</strong></p>
                <div style=\"margin-top: 20px; font-size: 12px; color: #999; text-align: center;\">
                    <p>&copy; " . date('Y') . " LearnHere. All rights reserved.</p>
                </div>
            </div>
        </body>
    </html>
    ";

    // Send the email
    $mail->send();
    echo "<script>alert('Registration notification sent to admin successfully.');</script>";
} catch (Exception $e) {
    // Error handling
    echo "<script>alert('Failed to send email. Error: {$mail->ErrorInfo}');</script>";
}

echo "<script>alert('Payment is Successfull.'); 
    window.location.href='sucessfullPayment.php';</script>";

} else {
    echo "Error: " . $stmt->error;
}
?>
