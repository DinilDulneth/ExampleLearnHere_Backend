
<?php
// Include database connection
require 'dbConfig.php';

//page security----------------------------------

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

//page security -------------------------------

// Get teacher details from session
$teacherID = $_SESSION['sTeacherID'];
$studentID = $_SESSION['studentID'];

// Fetch lessons for the given teacherID
$sql = "SELECT * FROM lesson WHERE teacherID = ?";
$stmt = $db_connection->prepare($sql);
$stmt->bind_param("i", $teacherID);
$stmt->execute();
$result = $stmt->get_result();

// Fetch teacher for the given teacherID
$sql = "SELECT * FROM teacher WHERE teacherID = ?";
$stmt = $db_connection->prepare($sql);
$stmt->bind_param("i", $teacherID);
$stmt->execute();
$result1 = $stmt->get_result();

if ($result1->num_rows > 0) {
    $teacher = $result1->fetch_assoc();
    $fName = htmlspecialchars($teacher['fName']);
    $lName = htmlspecialchars($teacher['lName']);
    $name = $fName . ' ' . $lName;
    $subject = htmlspecialchars($teacher['subject']);
    $profilePicture = htmlspecialchars($teacher['profilePicture']);
    $description = htmlspecialchars($teacher['description']);
} else {
    echo "Teacher not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessons by <?php echo htmlspecialchars($name); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .teacher-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .teacher-info h2 {
            margin: 0;
        }
        .lesson-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .lesson-box {
            width: 300px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
        .lesson-box img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .lesson-box h3 {
            margin: 0;
            font-size: 1.2em;
        }
        .lesson-box p {
            margin: 5px 0;
        }
        .lesson-box a {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
        }
        .lesson-box a:hover {
            background-color: #0056b3;
        }
    </style>

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
    <div class="teacher-info">
        <h2>Lessons by <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($subject); ?>)</h2>
        
        <!-- Teacher Profile Picture -->
        <?php if (!empty($teacher['profilePicture'])): ?>
            <div class="teacher-profile">
                <img src="<?php echo htmlspecialchars($teacher['profilePicture']); ?>" alt="Profile Picture of <?php echo htmlspecialchars($name); ?>" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
            </div>
        <?php endif; ?>
        
        <!-- Teacher Description -->
        <?php if (!empty($teacher['description'])): ?>
            <p><?php echo htmlspecialchars($teacher['description']); ?></p>
        <?php endif; ?>
        <a href='stdChatBox.php' style='color:inherit;'><i class='fa-regular fa-message'></i> Chat With your teacher</a>
        </div>
    </div>

    <div class="lesson-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="lesson-box">
                    <img src="<?php echo htmlspecialchars($row['thumbnailPicture']); ?>" alt="Lesson Thumbnail">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?></p>
                    <p><strong>Price:</strong> Rs. <?php echo htmlspecialchars($row['price']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                    <a href="stdLessonContent.php?lessonID=<?php echo $row['lessonID']; ?>&teacherID=<?php echo $teacherID; ?>&name=<?php echo urlencode($name); ?>&subject=<?php echo urlencode($subject); ?>">View Lesson Content</a>
                    <a href="Stdpaythis.php?lessonID=<?php echo $row['lessonID']; ?> &price=<?php echo htmlspecialchars($row['price']); ?> &teacherID=<?php echo $teacherID; ?> &paramstd=<?php echo $studentID; ?> &name=<?php echo urlencode($name); ?>&subject=<?php echo urlencode($subject); ?>">Pay Now</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No lessons available for this teacher.</p>
        <?php endif; ?>
    </div>

</body>
</html>
