<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Details</title>
    <style>
       /* Chat icon style */
       .chat-icon {
            position: relative;
            font-size: 30px; /* Bigger size for the chat icon */
            padding: 4px 8px 4px 8px;
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
            transition: all 0.3s ease; /* Smooth transition on hover */
            display: inline-block;
        }

        .chat-icon:hover {
            transform: scale(1.1); /* Zoom in effect on hover */
        }

        /* Style for the red circle indicating new messages */
        .new-message-indicator {
            position: absolute;
            top: 0;
            right: 0;
            width: 13px;
            height: 13px;
            background-color: red;
            border-radius: 50%;
            color: white;
            font-size: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Icon container to ensure proper alignment */
        .chat-container {
            display: flex;
            align-items: center;
        }

    </style>

    <!-- Font Awesome for chat icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

        $teacherID = $_SESSION['teacherID'];

        // SQL query to fetch students
        $sql = "SELECT * FROM student WHERE teacherID = ?";
        $stmt = $db_connection->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $teacherID);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if any students were found
            if ($result->num_rows > 0) {
                echo "<h1>Students Details</h1>";

                while ($row = $result->fetch_assoc()) {

                    // Check if there are new messages for the student
                    $studentID = $row['studentID'];
                    $messageSql = "SELECT * FROM messagestudentteacher WHERE studentID = ? AND isNew = 1";
                    $messageStmt = $db_connection->prepare($messageSql);
                    $messageStmt->bind_param("i", $studentID);
                    $messageStmt->execute();
                    $messageResult = $messageStmt->get_result();
                    $isNewMessage = $messageResult->num_rows > 0;

                    if (!empty($row['profilePicture'])) {
                        echo "<p></strong> <img src='" . htmlspecialchars($row['profilePicture']) . "' alt='Profile Picture' style='width:100px; height:100px; border-radius:50%; object-fit:cover;'></p>";
                    } else {
                        echo "<p><strong>Profile Picture:</strong> No image available</p>";
                    }
                    echo "<p><strong>Student ID:</strong> " . htmlspecialchars($row["studentID"]) . "</p>";
                    echo "<p><strong>First Name:</strong> " . htmlspecialchars($row["fName"]) . "</p>";
                    echo "<p><strong>Last Name:</strong> " . htmlspecialchars($row["lName"]) . "</p>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($row["email"]) . "</p>";
                    echo "<p><strong>Telephone:</strong> " . htmlspecialchars($row["telNo"]) . "</p>";
                    echo "<p><strong>Level:</strong> " . htmlspecialchars($row["level"]) . "</p>";
                    echo "<p><strong>Sign-in Date:</strong> " . htmlspecialchars($row["date"]) . "</p>";
                    echo "<p><strong>Device 1:</strong> " . htmlspecialchars($row["device1"]) . "</p>";
                    echo "<p><strong>Device 2:</strong> " . htmlspecialchars($row["device2"]) . "</p>";
                    echo "<p><strong>Device 3:</strong> " . htmlspecialchars($row["device3"]) . "</p>";
                    echo "<p><strong>Subject:</strong> " . htmlspecialchars($row["subject"]) . "</p>";
                    echo "<p><strong>Address:</strong> " . htmlspecialchars($row["address"]) . "</p>";
                    echo "<p><strong>School:</strong> " . htmlspecialchars($row["school"]) . "</p>";

                    echo "<form action='teachStudentPermission.php' method='POST' enctype='multipart/form-data'>";
                    if (htmlspecialchars($row["permission"])) {
                        echo "<strong>He/She has permission to log in to the system.</strong><br>";
                        echo "Turn Off the permission: <input type='radio' id='permission' name='permission' value='0' required><br>";
                    } else {
                        echo "<p><strong>He/She does not have permission to log in to the system.</strong></p>";
                        echo "Turn On the permission: <input type='radio' id='permission' name='permission' value='1' required><br>";
                    }
                    echo "<input type='hidden' id='studentID' name='studentID' value='" . htmlspecialchars($row['studentID']) . "'>";
                    echo "<input type='submit' value='Confirm'>";
                    echo "</form><br>";
                
                    $urlEdit = "teachChatWithStd.php?param1=" . urlencode($row["studentID"]) . "&param2=" . urlencode($row["fName"]) . "&param3=" . urlencode($row["profilePicture"]) ;
                    
                    // Use chat icon and add a red circle if there are new messages
                    echo "<div class='chat-icon'>";
                    if ($isNewMessage) {
                        // Red circle for new messages
                        echo "<div class='new-message-indicator'>!</div>";
                    }
                    echo "<a href='$urlEdit' style='color:inherit;'><i class='fa-regular fa-message'></i></i></a>"; // Chat icon
                    echo "</div>";
                    
                    echo "<hr>";
                }
            } else {
                echo "<p>No students found.</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Error: Unable to fetch data from the database.</p>";
        }

        $db_connection->close();
    ?>
</body>
</html>
