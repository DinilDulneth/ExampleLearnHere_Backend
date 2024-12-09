<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Content</title>
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

    <h2>Add Content</h2>

    <form action="teachAddLessonContentConfig.php" method="POST" enctype="multipart/form-data">

        <!-- lessonID hidden-->
        <input type="hidden" name="lessonID" id="lessonID" value="<?php echo $_SESSION['lessonID'] ?>" readonly>

        <!-- video: Video URL or file upload -->
        <label for="video">Upload Video:</label>
        <input type="file" name="video" id="video" accept="video/*" >
        <br><br>

        <!-- contentPicture: File upload -->
        <label for="contentPicture">Content Picture:</label>
        <input type="file" name="contentPicture" id="contentPicture" accept="image/*" required>
        <br><br>

        <!-- contentDescription: Text area for content description -->
        <label for="contentDescription">Content Description:</label>
        <textarea name="contentDescription" id="contentDescription" rows="4" cols="50" required></textarea>
        <br><br>

        <!-- pdfFile: File upload -->
        <label for="pdfFile">PDF File:</label>
        <input type="file" name="pdfFile" id="pdfFile" accept=".pdf" required>
        <br><br>

        <!-- Submit Button -->
        <button type="submit">Add Content</button>
    </form>

</body>
</html>