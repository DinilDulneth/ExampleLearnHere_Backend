

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
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

// Fetch student details from the database
$sql = "SELECT *FROM student";
if ($stmt = $db_connection->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "<p style='text-align: center;'>Error fetching student data.</p>";
    exit;
}
?>

    <h1 style="text-align: center;">Students</h1>

    <p style="text-align: center;">Here is the list of all students:</p>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Registered Teacher ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Level</th>
                <th>Enrollment Date</th>
                <th>Device 1</th>
                <th>Device 2</th>
                <th>Device 3</th>
                <th>Subject</th>
                <th>Address</th>
                <th>School</th>
                <th>Profile Picture</th>
                <th>Permission</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["studentID"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["teacherID"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["fName"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lName"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["telNo"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["level"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["date"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["device1"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["device2"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["device3"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["subject"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["school"]) . "</td>";
                    echo "<td><img src='" . (!empty($row["profilePicture"]) ? htmlspecialchars($row["profilePicture"]) : 'default-profile.png') . "' alt='Profile Picture'></td>";
                    echo "<td>" . htmlspecialchars($row["permission"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='13' style='text-align: center;'>No students found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div><a href="adminAddStudent.php">Add New Student</div>
</body>
</html>

<?php
// Close the database connection
$db_connection->close();
?>
