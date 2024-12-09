<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
        if (!isset($_SESSION['teacherID'])) {
            echo "<script> 
                    alert('Unauthorized access. Please log in.');
                    window.location.href = 'loginRegisterTec.php'; 
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
                    window.location.href = 'loginRegisterTec.php'; // Redirect to login page
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

    
    // Get the teacherID from the session
    $teacherID = $_SESSION['teacherID'];
    
    // SQL query to fetch the teacher's data
    $sql = "SELECT * FROM teacher WHERE teacherID = ?";  // Use prepared statements for security
    
    // Prepare the query
    if ($stmt = $db_connection->prepare($sql)) {
        $stmt->bind_param("i", $teacherID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Display the teacher's profile
            // If a profile picture exists, display it
            echo "<h1>My Profile</h1>";
            if (!empty($row['profilePicture'])) {
                echo "<img src='" . htmlspecialchars($row['profilePicture']) . "' alt='Profile Picture' style='width: 150px; height: 150px; border-radius: 50%; object-fit: cover;'>";
            }
            
            echo "<br><br>". htmlspecialchars($row['fName']) . " " . htmlspecialchars($row['lName']) ;
            echo "<p><strong>Subject:</strong> " . htmlspecialchars($row['subject']) . "</p>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
            echo "<p><strong>Phone Number:</strong> " . htmlspecialchars($row['telNo']) . "</p>";
            echo "<p><strong>Level:</strong> " . htmlspecialchars($row['level']) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
            echo "<p><strong>School:</strong> " . htmlspecialchars($row['school']) . "</p>";
            echo "<p><strong>Joined Date:</strong> " . htmlspecialchars($row['date']) . "</p>";
            
            
        } else {
            echo "<p>No data found for the given teacher ID.</p>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<p>Error preparing the query.</p>";
    }
    
    // Close the database connection
    $db_connection->close();
    ?>
    <p><a href="teachEditProfile.php">Edit profile details</p>
</body>
</html>
