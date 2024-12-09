<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        .dp{
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }

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


    <h1>Welcome to the Student Dashboard!</h1>

    <?php
    require 'dbConfig.php';

    if (isset($_SESSION['studentID'], $_SESSION['fName'], $_SESSION['sTeacherID'], $_SESSION['sprofilePicture'])) {
        echo '<p><img class="dp" src="' . htmlspecialchars($_SESSION['sprofilePicture']) . '" alt="Profile Picture" ></p>';
        echo "<p>Student ID: " . htmlspecialchars($_SESSION['studentID']) . "</p>";
        echo "<p>First Name: " . htmlspecialchars($_SESSION['fName']) . "</p>";
        echo "<p>Assigned Teacher ID: " . htmlspecialchars($_SESSION['sTeacherID']) . "</p>";
         } else {
        echo "<p>Error: Session data not set. Please log in again.</p>";
    }

    // Check if there are new messages for the student
    $studentID = $_SESSION['studentID'];
    $messageSql = "SELECT * FROM messagestudentteacher WHERE studentID = ? AND isNewStd = 1";
    $messageStmt = $db_connection->prepare($messageSql);
    $messageStmt->bind_param("i", $studentID);
    $messageStmt->execute();
    $messageResult = $messageStmt->get_result();
    $isNewMessage = $messageResult->num_rows > 0;

    // Use chat icon and add a red circle if there are new messages
    echo "<div class='chat-icon'>";
    if ($isNewMessage) {
        // Red circle for new messages
        echo "<div class='new-message-indicator'>!</div>";
    }
    echo "<a href='stdChatBox.php' style='color:inherit;'><i class='fa-regular fa-message'></i></i></a>"; // Chat icon
    echo "</div>";

    ?>
<br>
    <a href="StdClass.php">Class Room</a><br>
    <a href="stdLessons.php">My Lessons</a><br>
    <a href="stdPaymentHistory.php">Payment History</a><br>
    <a href="stdMyProfile.php">My profile</a><br>
    <a href="stdLogout.php">Logout</a><br>
    
</body>
</html>
