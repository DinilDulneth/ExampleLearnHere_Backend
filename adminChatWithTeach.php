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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
        
        .admin-message {
            background-color: #d1f7d1;
            align-self: flex-end;
            border-radius: 10px 10px 0 10px;
            margin-left: auto;
            margin-right: 10px;
            text-align: right;
        }

        .teacher-message {
            background-color: #f1f0f0;
            align-self: flex-start;
            border-radius: 10px 10px 10px 0;
            margin-left: 10px;
            text-align: left;
        }

        .new-message {
            font-weight: bold;
            color: #EF4C44;
            background-color: #ffeb3b;
            border-radius: 5px;
            padding: 5px;
            text-align: center;
            margin: 10px 0;
        }

        .form-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            width: 100%;
        }

        textarea {
            width: 500px;
            height: 50px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            resize: none;
            margin-right: 10px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
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
            top:78px
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
        if (!isset($_SESSION['adminID'])) {
            echo "<script> 
                    alert('Unauthorized access. Please log in.');
                    window.location.href = 'adminLogin.php'; 
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
                    window.location.href = 'adminLogin.php'; // Redirect to login page
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

    <h1 >Chat Box</h1>
    
    <!-- Chat Box -->
    <div class="chat-container">
    <div class="chat-box"  id="chat-box">
        <?php

        $adminID = $_SESSION['adminID'];
        
        $teacherID = isset($_GET['param1']) ? intval($_GET['param1']) : 0; 
        $teacherName = isset($_GET['param2']) ? htmlspecialchars($_GET['param2']) : '';
        $profilePic = isset($_GET['param3']) ? htmlspecialchars($_GET['param3']) : '';  

        require 'dbConfig.php';

        echo "
                    <div class='user-info'>
                        <img class='profilePic' src='" . (!empty($profilePic) ? htmlspecialchars($profilePic) : 'default-profile.png') . "' alt='Profile Picture'><br>
                        " . htmlspecialchars($teacherName) . "
                        <hr>
                    </div>
                    ";
        // Fetch chat messages
        $sql = "SELECT * FROM messageadminteacher WHERE  teacherID = ? ORDER BY date, time";
        if ($stmt = $db_connection->prepare($sql)) {
            $stmt->bind_param("i",$teacherID);
            $stmt->execute();
            $result = $stmt->get_result();

            $newMessageShown = false; // Flag to track if "New message" has been shown

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    // Show "New message" only once
                    if ($row['isNewAdmin'] == 1 && !$newMessageShown) {
                        echo "<div class='new-message'>New message</div>";
                        $newMessageShown = true; // Set the flag to true
                    }

                    if ($row["owner"] == 1) {
                        // Teacher's message
                        echo "<div class='chat-message teacher-message'>";
                        echo "<strong>Teacher:</strong> " . htmlspecialchars($row["message"]) . "<br>";
                        echo "<small>" . htmlspecialchars($row["date"]) . " " . htmlspecialchars($row["time"]) . "</small>";
                        echo "</div>";
                    } else if ($row["owner"] == 0) {
                        // Admin's message
                        echo "<div class='chat-message admin-message'>";
                        echo "<strong></strong> " . htmlspecialchars($row["message"]) . "<br>";
                        echo "<small>" . htmlspecialchars($row["date"]) . " " . htmlspecialchars($row["time"]) . "</small>";
                        echo "</div>";
                    }

                    // Mark the message as read (isNew = 0)
                    if ($row['isNewAdmin'] == 1) {
                        $updateSql = "UPDATE messageadminteacher SET isNewAdmin = 0 WHERE messageID = ?";
                        $updateStmt = $db_connection->prepare($updateSql);
                        $updateStmt->bind_param("i", $row['messageID']);
                        $updateStmt->execute();
                        $updateStmt->close();
                    }
                }
            } else {
                echo "<p style='text-align: center;'>No messages yet.</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='text-align: center;'>Error retrieving messages.</p>";
        }
        ?>
    </div>
</div>

    <!-- Form to send a message -->
    <div class="form-container">
        <form action="adminInsertMessage.php" method="POST">
            <textarea name="message" placeholder="Type your message here..." minlength="1" maxlength="350"  required></textarea><br>
            <input type="hidden" id="teacherID" name="teacherID" value="<?php echo $teacherID; ?>" readonly>
            <input type="hidden" id="teacherName" name="teacherName" value="<?php echo $teacherName; ?>" readonly>
            <input type="hidden" id="profilePic" name="profilePic" value="<?php echo $profilePic; ?>" readonly>
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
