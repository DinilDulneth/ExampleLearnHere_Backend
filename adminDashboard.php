<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

// Check if the admin is logged in
if (!isset($_SESSION['adminID'])) {
    // If not logged in, redirect to login page
    header("Location: adminLogin.php");
    exit;
}

// Include database configuration
require 'dbConfig.php';

// Initialize admin details (optional)
$adminID = $_SESSION['adminID'];
$adminEmail = $_SESSION['email']; // Email is stored during login

// Fetch admin details (optional, if you want more than the email)
$sql = "SELECT * FROM admin WHERE adminID = ?";
if ($stmt = $db_connection->prepare($sql)) {
    $stmt->bind_param("i", $adminID);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
}

?>

    <h1>Welcome to the Admin Dashboard</h1>
    <p>Hello, <strong><?php echo htmlspecialchars($adminID); ?></strong></p>

    <h2>Dashboard Options</h2>
    <ul>
        <li><a href="adminManageTeachers.php">Manage Teachers</a></li>
        <li><a href="adminPayment.php">View Payments</a></li>
        <li><a href="adminStudent.php">View Students</a></li>
        <li><a href="adminLogout.php">Logout</a></li>
    </ul>
</body>
</html>
