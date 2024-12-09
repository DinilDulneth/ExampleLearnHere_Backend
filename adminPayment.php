<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment, Student, and Lesson Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            width: 50px;
            height: auto;
            border-radius: 5px;
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

    <h1>Payment Details</h1>
    <?php
        require 'dbConfig.php';

        // SQL Query to join tables
        $sql = "SELECT 
                    p.paymentID, p.studentID AS pStudentID, p.teacherID AS pTeacherID, p.lessonID AS pLessonID, 
                    p.lessonPrice, p.paymentSlip, p.date AS paymentDate, p.time, 
                    s.studentID, s.fName, s.lName, s.email, s.telNo, 
                    s.level, s.subject, s.address, s.school, s.profilePicture, 
                    l.name AS lessonName, l.description AS lessonDescription, 
                    l.price AS lessonPrice, l.thumbnailPicture AS lessonThumbnail 
                FROM payment p
                INNER JOIN student s ON p.studentID = s.studentID
                INNER JOIN lesson l ON p.lessonID = l.lessonID";

        $stmt = $db_connection->prepare($sql);

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if any data was found
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Teacher ID</th>
                            <th>Lesson Name</th>
                            <th>Lesson Price</th>
                            <th>Payment Date</th>
                            <th>Payment Slip</th>
                            <th>Profile Picture</th>
                            <th>Lesson Thumbnail</th>
                        </tr>
                    </thead>";
                echo "<tbody>";

                // Fetch and display rows
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["paymentID"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["fName"]) . " " . htmlspecialchars($row["lName"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["pTeacherID"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lessonName"]) . "</td>";
                    echo "<td>Rs " . htmlspecialchars($row["lessonPrice"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["paymentDate"]) . " " . htmlspecialchars($row["time"]) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($row["paymentSlip"]) . "' alt='Payment Slip'></td>";
                    echo "<td><img src='" . htmlspecialchars($row["profilePicture"]) . "' alt='Profile Picture'></td>";
                    echo "<td><img src='" . htmlspecialchars($row["lessonThumbnail"]) . "' alt='Lesson Thumbnail'></td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p>No data found.</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Database error: Unable to prepare the query.</p>";
        }
    ?>
</body>
</html>
