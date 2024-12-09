<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson Content</title>
    <style>
        .contentPic{
            width: 200px;
            height: 150px;
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
    $lessonName = $_GET['param1'];
    $_SESSION['lessonName'] = $lessonName;
?>
    
<h2><?php  echo $lessonName?> contents</h2>
    <?php
        require 'dbConfig.php';

        $lessonID = isset($_GET['param3']) ? intval($_GET['param3']) : 0; // Secure integer cast
        $teacherID = isset($_GET['param2']) ? intval($_GET['param2']) : 0;  // (intval) Convert to integer

        $sql = "SELECT * 
                FROM lesson_content c , teacher t
                WHERE c.lessonID = $lessonID  AND t.teacherID = $teacherID";

        $result = $db_connection ->query($sql);

        //get content details
        if($result -> num_rows > 0){
            while($row = $result -> fetch_assoc()){
                echo "<img class='contentPic' src='" . $row["contentPicture"] . "'>";
                echo "<br> ";
                echo "<video width='320' height='240' controls>
                        <source src='" . $row["video"] . "' type='video/mp4'>
                        Your browser does not support the video tag.
                    </video>";
                echo " <br>";

                echo $row["contentDescription"];
                echo " <br>";

                echo "<a href='" . $row["pdfFile"] . "' target='_blank'>View PDF</a>";
                echo " <br>";

                $urlEdit = "teachEditContent.php?param1=" . urlencode($row["contentID"]) . "&param2=" . urlencode($row["contentDescription"]) . "&param3=" . urlencode($row["contentPicture"]) . "&param4=" . urlencode($row["video"] ) . "&param5=" . urlencode($row["pdfFile"] );
                echo "<button><a href='$urlEdit'>Edit Lesson</a></button>";
                
                echo " <br> <br>";
                echo "<br>"; 
            }
        }

        $_SESSION['lessonID'] = $lessonID;
    ?>
    <a href ="teachAddLessonContent.php">Add Content</a>
</body>
</html>