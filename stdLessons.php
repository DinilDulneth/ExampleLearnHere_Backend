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
<!-- page security -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
            // Reload the page if it is being loaded from cache
            window.onpageshow = function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            };
        </script>

</head>
<body>
    
<?php
// Include database connection
require 'dbConfig.php';

// Get teacherID and studentID from session
$teacherID = isset($_SESSION['sTeacherID']) ? $_SESSION['sTeacherID'] : '';
$studentID = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : '';

// Validate session data
if (empty($teacherID) || empty($studentID)) {
    echo "Invalid session data. Please log in again.";
    exit();
}

// SQL query to join the access and lesson tables
$sql = "
    SELECT 
        lesson.lessonID, 
        lesson.name, 
        lesson.price, 
        lesson.date AS lessonDate, 
        lesson.description, 
        lesson.thumbnailPicture, 
        access_table.startDate, 
        access_table.expireDate
    FROM 
        access_table
    INNER JOIN 
        lesson 
    ON 
        access_table.lessonID = lesson.lessonID
    WHERE 
        access_table.studentID = ? 
        AND access_table.teacherID = ?
";

// Prepare and execute the query
$stmt = $db_connection->prepare($sql);
$stmt->bind_param("ii", $studentID, $teacherID);
$stmt->execute();
$result = $stmt->get_result();
echo "<h1>My Lessons</h1>";
// Fetch and display the data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='lesson'>";
        if (!empty($row['thumbnailPicture'])) {
            echo "<img src='" . htmlspecialchars($row['thumbnailPicture']) . "' alt='" . htmlspecialchars($row['name']) . "' style='width:200px; height:auto;'>";
        }
        echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
        echo "<p><strong>Lesson Date:</strong> " . htmlspecialchars($row['lessonDate']) . "</p>";
        echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
        echo "<p><strong>Access Period:</strong> " . htmlspecialchars($row['startDate']) . " to " . htmlspecialchars($row['expireDate']) . "</p>";
        
        echo "<a href='stdLessonContent.php?lessonID=" . htmlspecialchars($row['lessonID']) . "'>view content</a>";
        echo "</div><hr>";
    }
} else {
    echo "<p>No lessons found for the given student and teacher.</p>";
}
?>

</body>
</html>