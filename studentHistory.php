<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student login History</title>
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
    $teachID = $_SESSION['teacherID'];
    require 'dbConfig.php';
        $sql = "SELECT * 
                from student s , studentloginhistory h 
                where s.studentID=h.studentID AND s.teacherID = $teachID"  ;

        $result = $db_connection ->query($sql);

        //get student login history details
        if($result -> num_rows > 0){
            while($row = $result -> fetch_assoc()){
                echo $row["loginID"];
                echo " ";
                echo $row["studentID"];
                echo " ";
                echo $row["loginDate"];
                echo " ";
                echo $row["time"];
                echo " ";
                echo $row["deviceFingerprint"];
                echo " ";
                echo $row["deviceInfo"];
                echo " ";
                echo "<br>";
            }
        }else{
            echo "No students found.";
        }
    ?>
</body>
</html>