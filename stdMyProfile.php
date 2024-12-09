<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .profile-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .profile-container h2 {
            text-align: center;
        }
        .profile-info {
            margin-bottom: 20px;
            font-size: 1.2em;
        }
        .profile-info strong {
            display: inline-block;
            width: 150px;
        }
        .edit-button {
            display: block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            width: 150px;
            margin: 0 auto;
        }
        .edit-button:hover {
            background-color: #0056b3;
        }
    </style>
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
    echo "Student not found.";
    exit();
}
?>

<div class="profile-container">
    <h2>My Profile</h2>
    <div class="profile-info">
        <p><strong>First Name:</strong> <?php echo htmlspecialchars($student['fName']); ?></p>
        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($student['lName']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($student['telNo']); ?></p>
        <p><strong>Level:</strong> <?php echo htmlspecialchars($student['level']); ?></p>
        <p><strong>Subject:</strong> <?php echo htmlspecialchars($student['subject']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($student['address']); ?></p>
        <p><strong>Registered On:</strong> <?php echo htmlspecialchars($student['date']); ?></p>
    </div>
    <a href="stdEditMyProfile.php" class="edit-button">Edit Profile</a>
</div>
</body>
</html>
