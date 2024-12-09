<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content Details</title>
    <style>
        .thumbnail-preview {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 10px;
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
        require 'dbConfig.php';
        // Fetch parameters securely from the URL using `GET`
        $contentID = isset($_GET['param1']) ? intval($_GET['param1']) : 0; 
        $description = isset($_GET['param2']) ? htmlspecialchars($_GET['param2']) : ''; 
        $thumbnailPicture = isset($_GET['param3']) ? htmlspecialchars($_GET['param3']) : '';
        $video = isset($_GET['param4']) ? htmlspecialchars($_GET['param4']) : '';
        $pdfFile = isset($_GET['param5']) ? htmlspecialchars($_GET['param5']) : '';
        
        $_SESSION['contentID'] = $contentID;
        echo "<h2>" .$_SESSION['lessonName']." Content</h2>"; 
    ?>

    <!-- Edit Form -->
    <form action="teachEditContentConfig.php" method="POST" enctype="multipart/form-data">
        <!-- Hidden Field to Pass Lesson ID -->
        
        <label for="description">Description:</label>
        <textarea 
            name="description" 
            id="description" 
            maxlength="900" 
            minlength="1"><?php echo $description; ?></textarea>
        <br><br>

        <button type="submit">Confirm</button>

        <br><br>
        If you want to delete your content : 
        <?php
            $urlDelete = "teachDeleteLessonContent.php?param1=" . urlencode($contentID) . "&param2=" . urlencode($thumbnailPicture) . "&param3=" . urlencode($video) . "&param4=" .urlencode($pdfFile); 
            echo "<button><a href='$urlDelete'>Delete Content</a></button>";
        ?>
    </form>
</body>
</html>
