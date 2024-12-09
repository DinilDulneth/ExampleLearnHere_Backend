<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Box</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
        }
        .chat-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .chat-box {
            width: 50%;
            max-width: 600px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-y: auto;
            height: 400px;
            padding: 15px;
        }
        .chat-message {
            margin-bottom: 15px;
            max-width: 75%;
            padding: 10px;
            border-radius: 10px;
            word-wrap: break-word;
            font-size: 14px;
        }
        .student-message {
            background-color: #d1f7d1;
            align-self: flex-start;
            border-radius: 10px 10px 0 10px;
            align-self: flex-end;
            margin-left: auto;
            word-wrap: break-word;
        }
        .teacher-message {
            background-color: #f1f0f0;
            align-self: flex-end;
            border-radius: 10px 10px 10px 0;
        }
        .new-message {
            font-weight: bold;
            color: #EF4C44;
            background-color: #ffeb3b;
            border-radius: 5px;
            padding: 2px;
            text-align: center;
            margin: 10px 0;
        }
        .form-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        textarea {
            width: 90%;
            height: 18px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .profilePic{
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            padding: 9px;
        }
        .user-info{
            position:absolute;
            background-color: white;
            width: 600px;
            top:20px
        }
        /* Add responsiveness for smaller devices */
        @media screen and (max-width: 768px) {
            .chat-box {
                width: 95%;
                height: 350px;
            }

            textarea {
                width: 70%;
                height: 40px;
            }

            button {
                padding: 8px 15px;
                font-size: 12px;
            }
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
<?php
require 'dbConfig.php';

$studentID = $_SESSION['studentID'];

// Fetch teacher's details
$sql_teacher = "SELECT teacher.fName, teacher.profilePicture 
                FROM teacher 
                INNER JOIN student ON teacher.teacherID = student.teacherID 
                WHERE student.studentID = ?";
$stmt_teacher = $db_connection->prepare($sql_teacher);
$stmt_teacher->bind_param("i", $studentID);
$stmt_teacher->execute();
$result_teacher = $stmt_teacher->get_result();



if ($result_teacher->num_rows > 0) {
    $teacher = $result_teacher->fetch_assoc();
    $teacherName = htmlspecialchars($teacher['fName']);
    $teacherPicture = htmlspecialchars($teacher['profilePicture']);

    $_SESSION['tFname'] = $teacher['fName'];
    $_SESSION['tProfilePicture'] = $teacher['profilePicture'];

} else {
    $teacherName = "Teacher";
    $teacherPicture = "default-profile.png"; // Default picture if none exists
}
$stmt_teacher->close();
?>

<div class="chat-container">
    <!-- Chat Header -->
    <div class="user-info">
        <img class="profilePic" src="<?php echo $teacherPicture; ?>" alt="Teacher Picture">
        <?php echo $teacherName; ?>
    </div>

    <!-- Chat Messages -->
    <div class="chat-box" id="chat-box">
        <?php

        $teacherID = $_SESSION['sTeacherID'];

        $sql = "SELECT * FROM messagestudentteacher WHERE studentID = ? ORDER BY date, time";
        $stmt_messages = $db_connection->prepare($sql);
        $stmt_messages->bind_param("i", $studentID);
        $stmt_messages->execute();
        $result_messages = $stmt_messages->get_result();

        $newMessageShown = false;

        if ($result_messages->num_rows > 0) {
            while ($row = $result_messages->fetch_assoc()) {

                // Show "New message" only once
                if ($row['isNewStd'] == 1 && !$newMessageShown) {
                    echo "<div class='new-message'>New message</div>";
                    $newMessageShown = true; // Set the flag to true
                }

                if ($row['owner'] == 0) {
                    // Student messages
                    echo "<div class='chat-message student-message'>";
                    echo "<strong></strong> " . htmlspecialchars($row["message"]) . "<br>";
                    echo "<small>" . htmlspecialchars($row["date"]) . " " . htmlspecialchars($row["time"]) . "</small>";
                    echo "</div>";
                } else if ($row['owner'] == 1) {
                    // Teacher messages
                    echo "<div class='chat-message teacher-message'>";
                    echo "<strong>Teacher:</strong> " . htmlspecialchars($row["message"]) . "<br>";
                    echo "<small>" . htmlspecialchars($row["date"]) . " " . htmlspecialchars($row["time"]) . "</small>";
                    echo "</div>";
                }

                // Mark the message as read (isNewStd = 0)
                if ($row['isNewStd'] == 1) {
                    $updateSql = "UPDATE messagestudentteacher SET isNewStd = 0 WHERE messageID = ?";
                    $updateStmt = $db_connection->prepare($updateSql);
                    $updateStmt->bind_param("i", $row['messageID']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            }
        } else {
            echo "<p style='text-align: center;'>No messages yet.</p>";
        }
        ?>
    </div>
</div>

<!-- Form to send a message -->
<div class="form-container">
    <form action="stdInsertMessages.php" method="POST">
        <textarea name="message" placeholder="Type your message here..." minlength="1" maxlength="350" required></textarea>
        <button type="submit">Send</button>
    </form>
</div>

<script>
    // Scroll to the bottom of the chat box when a new message is added
    const chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>
