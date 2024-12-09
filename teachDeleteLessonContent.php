<?php
require 'dbConfig.php';
session_start();

// Get the content ID and file paths from the URL parameters
$contentID = isset($_GET['param1']) ? intval($_GET['param1']) : 0; // Validate contentID as an integer
$thumbnailPic = isset($_GET['param2']) ? $_GET['param2'] : '';
$video = isset($_GET['param3']) ? $_GET['param3'] : '';
$pdfFile = isset($_GET['param4']) ? $_GET['param4'] : '';

// Validate the session variables
if (!isset($_SESSION['teacherID'])) {
    echo "<script>
            alert('Unauthorized access. Please log in.');
            window.location.href = 'loginPage.php';
          </script>";
    exit;
}

$teacherID = $_SESSION['teacherID'];

// Validate contentID before proceeding
if ($contentID <= 0) {
    echo "<script>
            alert('Invalid content ID.');
            window.location.href = 'teachLessonContentView.php';
          </script>";
    exit;
}

// Prepare the DELETE query
$sql = "DELETE FROM lesson_content WHERE contentID = ?";
$stmt = $db_connection->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $contentID); // Bind contentID as an integer

    if ($stmt->execute()) {
            unlink($thumbnailPic); // Delete the thumbnail file
            unlink($video); // Delete the video file
            unlink($pdfFile);

        // Fetch related lesson details to redirect back properly
        $sql1 = "SELECT name, teacherID, lessonID FROM lesson WHERE teacherID = ?";
        $stmt1 = $db_connection->prepare($sql1);

        if ($stmt1) {
            $stmt1->bind_param("i", $teacherID);
            $stmt1->execute();
            $result = $stmt1->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row['name'];
                $teacherID = $row['teacherID'];
                $lessonID = $row['lessonID'];

                // Redirect to the lesson content view page
                echo "<script>
                        alert('Content Deleted Successfully!');
                        window.location.href = 'teachLessonContentView.php?param1=" . urlencode($name) . "&param2=" . urlencode($teacherID) . "&param3=" . urlencode($lessonID) . "';
                      </script>";
            } else {
                echo "<script>
                        alert('Lesson details not found.');
                        window.location.href = 'teachLessonContentView.php';
                      </script>";
            }

            $stmt1->close();
        } else {
            echo "<script>
                    alert('Error retrieving lesson details.');
                  </script>";
        }
    } else {
        echo "<script>
                alert('Error deleting content. Please try again.');
                window.location.href = 'teachLessonContentView.php';
              </script>";
    }

    $stmt->close();
} else {
    echo "<script>
            alert('Database error: Unable to prepare the statement.');
            window.location.href = 'teachLessonContentView.php';
          </script>";
}
?>
