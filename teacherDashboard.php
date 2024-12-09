<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .profilePic{
            width: 150px;                
            height: 150px;                
            object-fit:cover; 
            border-radius: 50%;
        }
        .fa-comments{
            text-decoration: none;
            font-size: 23px;
            color: #4CAF50;
            margin-left: 90px;
            padding: 5px;
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


    <a href="teachProfile.php">
        <!-- Display the profile picture -->
        <div class="profilePic">
            <img class="profilePic" src="<?php echo htmlspecialchars($_SESSION['teacherProfilePic']); ?>" alt="Profile Picture">
        </div>
    
        <!-- Display the teacher's name -->
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['teacherName']); ?>!</p>
    </a>

        <?php
            if (!isset($_SESSION['teacherID'])) { 
            echo "<script> alert('Unauthorized access. Please log in.');
                window.location.href = 'LoginRegisterTec.php'; </script>"; exit; 
            }
        ?>

        <a href="teachLessonsPage.php">My Lessons</a><br>
        <a href="studentHistory.php">Studetn Login History</a><br>
        <a href="teachStudents.php">Students</a><br>
        <a href="teachStdPayments.php">Payment</a><br>
        <a href="teachProfile.php">My Profile</a><br>
        <a href="teachEditProfile.php">Edit My Profile</a><br>
        <a href="teachLogout.php">Logout</a><br>

        <?php
        require 'dbConfig.php';
                    $teacherIDAdmin = $_SESSION['teacherID'];
                    $sqlisNewAdmin = "SELECT * 
                                    FROM messageadminteacher m , teacher t
                                    WHERE t.teacherID=m.teacherID AND
                                        t.teacherID = $teacherIDAdmin AND
                                        isNew = 1;";
                                        
                    $resultIsNew = $db_connection ->query($sqlisNewAdmin);

                    $iconColor = ($resultIsNew->num_rows > 0) ? "color:#FF0000;" : "color: #4CAF50;";

                    $urlEdit = "";
                    echo "<a href='teachChatWithAdmin.php' title='Chat'>
                        <i class='fa fa-comments' style='$iconColor'></i>
                    Chat with Admin</a>";
        ?>
</body>
</html>