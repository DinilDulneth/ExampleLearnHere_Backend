<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit My Profile</title>
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
    
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect form data
        $fName = $_POST['fName'];
        $lName = $_POST['lName'];
        $email = $_POST['email'];
        $telNo = $_POST['telNo'];
        $description = $_POST['description'];
        $school = $_POST['school'];

        // If a new profile picture is uploaded, handle it
        if ($_FILES['profilePicture']['error'] == 0) {
            // Get the current profile picture path
            $sql = "SELECT profilePicture FROM teacher WHERE teacherID = ?";
            if ($stmt = $db_connection->prepare($sql)) {
                $stmt->bind_param("i", $teacherID);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $currentProfilePicture = $row['profilePicture'];

                // Delete the previous profile picture from the server if exists
                if (!empty($currentProfilePicture) && file_exists($currentProfilePicture)) {
                    unlink($currentProfilePicture);
                }

                // Save the new profile picture
                $profilePicture = 'Upload/teacherProfilePicture/' . basename($_FILES['profilePicture']['name']);
                move_uploaded_file($_FILES['profilePicture']['tmp_name'], $profilePicture);
            }
        } else {
            // No new profile picture uploaded, keep the current one
            $profilePicture = $_POST['currentProfilePicture'];
        }

        // Update the teacher profile in the database
        $sql = "UPDATE teacher SET fName = ?, lName = ?, email = ?, telNo = ?, description = ?, school = ?, profilePicture = ? WHERE teacherID = ?";
        if ($stmt = $db_connection->prepare($sql)) {
            $stmt->bind_param("sssssssi", $fName, $lName, $email, $telNo, $description, $school, $profilePicture, $teacherID);
            
            if ($stmt->execute()) {
                echo "<script>alert('Edit successfully!'); 
                    window.location.href='teachProfile.php';</script>";
            } else {
                echo "<script>alert('Error updating profile. Please try again later!'); 
                    window.location.href='teachProfile.php';</script>";
            }

            $stmt->close();
        } else {
            echo "<p>Error preparing the query.</p>";
        }
    }

    // Fetch the current teacher's data
    $sql = "SELECT * FROM teacher WHERE teacherID = ?";
    if ($stmt = $db_connection->prepare($sql)) {
        $stmt->bind_param("i", $teacherID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Display the form with the current values
            echo "<h1>Edit My Profile</h1>";

            echo "<form method='post' enctype='multipart/form-data'>";
            echo "<p><strong>First Name:</strong> <input type='text' name='fName' value='" . htmlspecialchars($row['fName']) . "' required></p>";
            echo "<p><strong>Last Name:</strong> <input type='text' name='lName' value='" . htmlspecialchars($row['lName']) . "' required></p>";
            echo "<p><strong>Email:</strong> <input type='email' name='email' value='" . htmlspecialchars($row['email']) . "' required></p>";
            echo "<p><strong>Phone Number:</strong> <input type='text' name='telNo' value='" . htmlspecialchars($row['telNo']) . "' required></p>";
            echo "<p><strong>Description:</strong> <textarea name='description' required>" . htmlspecialchars($row['description']) . "</textarea></p>";
            echo "<p><strong>School:</strong> <input type='text' name='school' value='" . htmlspecialchars($row['school']) . "' required></p>";
            
            // If a profile picture exists, show it
            if (!empty($row['profilePicture'])) {
                echo "<p><strong>Current Profile Picture:</strong><br>";
                echo "<img src='" . htmlspecialchars($row['profilePicture']) . "' alt='Profile Picture' style='width: 150px; height: 150px; border-radius: 50%;'></p>";
            }
            
            echo "<p><strong>Upload New Profile Picture:</strong> <input type='file' name='profilePicture'></p>";
            echo "<input type='hidden' name='currentProfilePicture' value='" . htmlspecialchars($row['profilePicture']) . "'>";
            echo "<p><button type='submit'>Update Profile</button></p>";
            echo "</form>";
        } else {
            echo "<p>No data found for the given teacher ID.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Error preparing the query.</p>";
    }

    // Close the database connection
    $db_connection->close();
    ?>
</body>
</html>
