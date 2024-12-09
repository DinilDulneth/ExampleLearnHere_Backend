<?php
require 'dbConfig.php';
session_start();
// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $contentID = $_SESSION['contentID'];
    $cDescription = $_POST['description'] ;

    // Update query
    $sql = "UPDATE lesson_content SET contentDescription = ? WHERE contentID = ?";
    $stmt = $db_connection->prepare($sql);

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param('si', $cDescription, $contentID);

        // Execute and check if the update was successful
        if ($stmt->execute()) {
            echo "<script>
                    alert('Content details updated successfully!');
                    window.location.href = 'teachLessonContentView.php?param1=" . urlencode($_SESSION['lessonName']) . "&param2=" . urlencode($_SESSION['teacherID']) . "&param3=" . urlencode($_SESSION['lessonID']) . "';
                </script>";
          } else {
            echo "<script>
                    alert('Error editing content details, Please try again!');
                    window.location.href = 'teachLessonContentView.php?param1=" . urlencode($_SESSION['lessonName']) . "&param2=" . urlencode($_SESSION['teacherID']) . "&param3=" . urlencode($_SESSION['lessonID']) . "';
                  </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
                alert('Database error: Unable to prepare the statement.');
                window.location.href = 'teachLessonContentView.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid request method.');
            window.location.href = 'teachLessonContentView.php';
          </script>";
}
?>
