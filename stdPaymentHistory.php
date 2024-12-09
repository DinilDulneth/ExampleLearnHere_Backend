<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<!-- for page security -->
<?php
        session_start(); // Start the session at the very top
        
        // Prevent caching
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Validate session variable
        if (!isset($_SESSION['studentID'])) {
            echo "<script> 
                    alert('Unauthorized access. Please log in.');
                    window.location.href = 'loginRegisterStd.php'; 
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
                    window.location.href = 'loginRegisterStd.php'; // Redirect to login page
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
// Include database connection
require 'dbConfig.php';

// Get studentID from session
$studentID = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : '';

// Validate session data
if (empty($studentID)) {
    echo "Invalid session data. Please log in again.";
    exit();
}

// SQL query to fetch payment history for the student
$sql = "
    SELECT 
        payment.paymentID,
        payment.teacherID,
        payment.lessonID,
        payment.lessonPrice,
        payment.paymentSlip,
        payment.date,
        payment.time,
        payment.isNew,
        lesson.name AS lessonName,
        teacher.fName AS teacherFirstName,
        teacher.lName AS teacherLastName
    FROM 
        payment
    LEFT JOIN 
        lesson ON payment.lessonID = lesson.lessonID
    LEFT JOIN 
        teacher ON payment.teacherID = teacher.teacherID
    WHERE 
        payment.studentID = ?
    ORDER BY 
        payment.date DESC, payment.time DESC
";

// Prepare and execute the query
$stmt = $db_connection->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();
echo "<h1>Payment History</h1>";
// Display the payment history
if ($result->num_rows > 0) {
    echo "<table border='1' style='width:100%; text-align:left;'>";
    echo "<tr>
            <th>Payment ID</th>
            <th>Lesson Name</th>
            <th>Teacher Name</th>
            <th>Lesson Price</th>
            <th>Payment Slip</th>
            <th>Date</th>
            <th>Time</th>
          </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['paymentID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['lessonName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['teacherFirstName'] . ' ' . $row['teacherLastName']) . "</td>";
        echo "<td>Rs. " . htmlspecialchars($row['lessonPrice']) . "</td>";
        echo "<td>";
        if (!empty($row['paymentSlip'])) {
            echo "<a href='" . htmlspecialchars($row['paymentSlip']) . "' target='_blank'>View Slip</a>";
        } else {
            echo "N/A";
        }
        echo "</td>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['time']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No payment history found for this student.</p>";
}
?>

</body>
</html>