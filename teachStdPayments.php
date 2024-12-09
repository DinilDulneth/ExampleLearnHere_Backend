<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        img {
            width: 100px;
            height: auto;
            border-radius: 5px;
            object-fit: cover;
        }
        h1 {
            margin-bottom: 20px;
        }
        .section-title {
            margin-top: 40px;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: bold;
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

    $teacherID = $_SESSION['teacherID']; // Assuming teacherID is stored in session

    // SQL query to retrieve payment details
    $sql = "SELECT 
                p.paymentID, p.paymentSlip, p.date AS paymentDate, p.time, 
                p.studentID, p.teacherID AS pTeacherID, p.lessonID AS pLessonID,
                s.fName, s.lName, s.email, s.telNo, s.address, s.school, s.profilePicture, 
                l.name, l.description, l.price, 
                a.expireDate, p.isNew
            FROM payment p
            INNER JOIN student s ON p.studentID = s.studentID
            INNER JOIN lesson l ON p.lessonID = l.lessonID
            LEFT JOIN access_table a ON p.lessonID = a.lessonID AND p.studentID = a.studentID
            WHERE p.teacherID = ?
            ORDER BY p.date DESC";

    $stmt = $db_connection->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $teacherID);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            // Check if any data was found
            if ($result->num_rows > 0) {
                echo "<h1>Payment Details</h1>";
                
                // Unread payments section
                echo "<div class='section-title'>Unread Payments</div>";
                echo "<table>";
                echo "<thead>
                        <tr>
                            <th>Profile Picture</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Address</th>
                            <th>School</th>
                            <th>Lesson Name</th>
                            <th>Lesson Price (Rs)</th>
                            <th>Payment Slip</th>
                            <th>Payment Date</th>
                            <th>Payment Time</th>
                            <th>Expire Date</th>
                            <th>Access Control</th>
                        </tr>
                      </thead>";
                echo "<tbody>";
                while ($row = $result->fetch_assoc()) {
                    if ($row["isNew"] == 1) {
                        // Render the row
                        renderRow($row);

                    }
                }
                echo "</tbody></table>";

                // Read payments section
                echo "<div class='section-title'>Read Payments</div>";
                echo "<table>";
                echo "<thead>
                        <tr>
                            <th>Profile Picture</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Address</th>
                            <th>School</th>
                            <th>Lesson Name</th>
                            <th>Lesson Price (Rs)</th>
                            <th>Payment Slip</th>
                            <th>Payment Date</th>
                            <th>Payment Time</th>
                            <th>Expire Date</th>
                            <th>Access Control</th>
                        </tr>
                      </thead>";
                echo "<tbody>";
                $result->data_seek(0); // Reset pointer for reuse
                while ($row = $result->fetch_assoc()) {
                    if ($row["isNew"] == 0) {
                        renderRow($row);
                    }
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No payment data found for the given teacher ID.</p>";
            }
        } else {
            echo "<p>Error executing the query: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Database error: Unable to prepare the query.</p>";
    }

    function renderRow($row) {
        $today = date('Y-m-d');
        echo "<tr>";
        echo "<td><img src='" . htmlspecialchars($row["profilePicture"]) . "' alt='Profile Picture'></td>";
        echo "<td>" . htmlspecialchars($row["fName"]) . " " . htmlspecialchars($row["lName"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["telNo"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["school"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
        echo "<td>Rs " . htmlspecialchars($row["price"]) . "</td>";
        echo "<td><img src='" . htmlspecialchars($row["paymentSlip"]) . "' alt='Payment Slip'></td>";
        echo "<td>" . htmlspecialchars($row["paymentDate"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["time"]) . "</td>";
        echo "<td>";

        echo "<form method='post' action='tachGetAccess.php'>";
        echo $row["expireDate"] ? htmlspecialchars($row["expireDate"]) : "Not Set";
        echo "<input type='hidden' name='lessonID' value='" . htmlspecialchars($row["pLessonID"]) . "'>";
        echo "<input type='hidden' name='studentID' value='" . htmlspecialchars($row["studentID"]) . "'>";
        echo "<input type='hidden' name='teacherID' value='" . htmlspecialchars($row["pTeacherID"]) . "'>";
        echo "<input type='hidden' name='paymentID' value='" . htmlspecialchars($row["paymentID"]) . "'>";
        
        echo "</td>";
        echo "<td>";
        
        echo "<label>
                Expire Date:
                <input type='date' name='expireDate' value='" . htmlspecialchars($row['expireDate']) . "' min='" . $today . "' required>
              </label>
              <br><br>";
        
        echo "<button type='submit'>Update</button>";
        echo "</form>";
        
        echo "</td>";
        echo "</tr>";
    }
    ?>
</body>
</html>
