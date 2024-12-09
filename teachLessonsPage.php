<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .thumbnailPic{
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit:cover; 
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


<h2>My Lessons</h2>
    
    <?php
        require "dbConfig.php";

        $teacherID = $_SESSION['teacherID'];
        
        $sql = "SELECT * FROM lesson WHERE teacherID= $teacherID";
        $result = $db_connection ->query($sql);

        //get student login history details
        if($result -> num_rows > 0){
            while($row = $result -> fetch_assoc()){
                $url = "teachLessonContentView.php?param1=" . urlencode($row["name"]) . "&param2=" . urlencode($row["teacherID"]) . "&param3=" . urlencode($row["lessonID"]);
                echo "<a href='$url'>";

                echo "<img class='thumbnailPic' src='" . $row["thumbnailPicture"] . "'>";
                echo "<br> ";
                echo $row["name"];
                echo " <br>";
                echo $row["description"];
                echo " <br>";
                echo "Rs.". $row["price"];
                echo " <br>";
                echo $row["date"];
                echo " <br></a>";
                

                $urlEdit = "teachEditLesson.php?param1=" . urlencode($row["lessonID"]) . "&param2=" . urlencode($row["price"]) . "&param3=" . urlencode($row["name"]) . "&param4=" . urlencode($row["date"]) . "&param5=" . urlencode($row["description"]) . "&param6=" . urlencode($row["thumbnailPicture"]);
                echo "<button><a href='$urlEdit'>Edit Lesson</a></button>";
                echo " <br> <br>";
            }
        }

    ?>

    <a href="createLesson.php">Create new Lesson</a>
</body>
</html>