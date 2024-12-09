<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .edit-profile-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .edit-profile-container h2 {
            text-align: center;
        }
        .edit-profile-container form {
            margin-top: 20px;
        }
        .edit-profile-container form input, .edit-profile-container form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 1em;
        }
        .edit-profile-container form button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
        }
        .edit-profile-container form button:hover {
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
    echo "<script>alert('Student not found. Please try again later.'); window.location.href='login.php';</script>";
    exit();
}
?>

<div class="edit-profile-container">
    <h2>Edit Profile</h2>
    <form action="stdEditMyProfileConfig.php" method="POST">
        <label for="fName">First Name:</label>
        <input type="text" name="fName" id="fName" maxlength="20"  value="<?php echo htmlspecialchars($student['fName']); ?>" required>

        <label for="lName">Last Name:</label>
        <input type="text" name="lName" id="lName" maxlength="20"  value="<?php echo htmlspecialchars($student['lName']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" maxlength="50"  value="<?php echo htmlspecialchars($student['email']); ?>" required>

        <label for="telNo">Phone Number:</label>
        <input type="text" name="telNo" id="telNo" maxlength="15"  value="<?php echo htmlspecialchars($student['telNo']); ?>" required>
        
        <label for="address">Address:</label>
        <textarea name="address" id="address" maxlength="100"  rows="4" required><?php echo htmlspecialchars($student['address']); ?></textarea>

        <button type="submit">Save Changes</button>
    </form>
</div>
</body>
</html>
