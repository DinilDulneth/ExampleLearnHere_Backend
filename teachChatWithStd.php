<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Box</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .chat-box {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-height: 400px;
            overflow-y: auto;
        }
        .chat-message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }
        .teacher-message {
            background-color: #e1f5fe;
            color: #01579b;
            align-self: flex-start;
            margin-left: 220px;
        }
        .student-message {
            background-color: #c8e6c9;
            color: #1b5e20;
            align-self: flex-end;
        }
        .form-container {
            width: 60%;
            margin: 20px auto;
            text-align: center;
        }
        textarea {
            width: 80%;
            height: 20px;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .new-message {
            font-weight: bold;
            color: #ff5722;
            text-align: center;
            margin-bottom: 20px;
        }
        .user-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            padding: 5px;
        }
        .user-info {
            display: flex;
            flex-direction: row;
            height: 58px;
            width:666px;
            text-align: center;
            background-color: white;
            position: absolute;
            align-items: center;
            margin-left: 298px;

        }
        .user-info h5 {
            margin-top: 6px;
        }
    </style>
</head>
<body>

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

<?php
require 'dbConfig.php';

// Get teacher ID from session
$teachID = $_SESSION['teacherID'];

// Get student details from query parameters
$studentID = isset($_GET['param1']) ? intval($_GET['param1']) : 0; 
$fName = isset($_GET['param2']) ? htmlspecialchars($_GET['param2']) : ''; 
$profilePicture = isset($_GET['param3']) ? htmlspecialchars($_GET['param3']) : ''; 
?>

<!-- Student Info -->
<div class="user-info">
    <?php if (!empty($profilePicture)) { ?>
        <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture">
    <?php } else { ?>
        <img src="default-profile.png" alt="No Profile Picture">
    <?php } ?>
    <h5><?php echo $fName; ?></h5>
</div>

<!-- Chat Box -->
<div class="chat-box" id="chat-box">
    <?php
    $newMessageShown = false; // Flag for showing the "New message" label

    // Fetch chat messages
    $sql = "SELECT * FROM messagestudentteacher WHERE teacherID = ? AND studentID = ? ORDER BY date, time";
    $stmt_messages = $db_connection->prepare($sql);
    $stmt_messages->bind_param("ii", $teachID, $studentID);
    $stmt_messages->execute();
    $result_messages = $stmt_messages->get_result();

    if ($result_messages->num_rows > 0) {
        while ($row = $result_messages->fetch_assoc()) {
            // Show "New message" once
            if ($row['isNew'] == 1 && !$newMessageShown) {
                echo "<div class='new-message'>New message</div>";
                $newMessageShown = true;
            }

            // Mark the message as read
            if ($row['isNew'] == 1) {
                $updateSql = "UPDATE messagestudentteacher SET isNew = 0 WHERE messageID = ?";
                $updateStmt = $db_connection->prepare($updateSql);
                $updateStmt->bind_param("i", $row['messageID']);
                $updateStmt->execute();
                $updateStmt->close();
            }

            echo "<div class='chat-message ";
            echo $row['owner'] == 0 ? 'student-message' : 'teacher-message';
            echo "'>";
            echo "<strong>" . ($row['owner'] == 0 ? 'Student' : 'Teacher') . ":</strong> " . htmlspecialchars($row['message']) . "<br>";
            echo "<small>" . htmlspecialchars($row['date']) . " " . htmlspecialchars($row['time']) . "</small>";
            echo "</div>";
        }
    } else {
        echo "<p>No messages yet.</p>";
    }
    ?>
</div>

<!-- Form to send a message -->
<div class="form-container">
    <form action="teachInsertMessageStd.php" method="POST">
        <input type="hidden" name="studentID" value="<?php echo $studentID; ?>">
        <input type="hidden" name="profilePicture" value="<?php echo htmlspecialchars($profilePicture); ?>">
        <input type="hidden" name="fName" value="<?php echo htmlspecialchars($fName); ?>">
        <textarea name="message" placeholder="Type your message here..." required></textarea><br>
        <button type="submit">Send</button>
    </form>
</div>

<script>
    // Scroll to the bottom of the chat box when new messages are added
    const chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>
