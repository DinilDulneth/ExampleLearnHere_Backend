<?php
// Include database connection
require 'dbConfig.php';

// Get lesson and teacher details from URL parameters
$lessonID = isset($_GET['lessonID']) ? $_GET['lessonID'] : '';
$teacherID = isset($_GET['teacherID']) ? $_GET['teacherID'] : '';

// Fetch teacher details
$sql = "SELECT * FROM teacher WHERE teacherID = ?";
$stmt = $db_connection->prepare($sql);
$stmt->bind_param("i", $teacherID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $teacher = $result->fetch_assoc();
    $fName = htmlspecialchars($teacher['fName']);
    $lName = htmlspecialchars($teacher['lName']);
    $subject = htmlspecialchars($teacher['subject']);
} else {
    echo "Teacher not found.";
    exit();
}

// Fetch lesson details based on lessonID and teacherID
$sql = "SELECT * FROM lesson WHERE lessonID = ? AND teacherID = ?";
$stmt = $db_connection->prepare($sql);
$stmt->bind_param("ii", $lessonID, $teacherID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $lesson = $result->fetch_assoc();
} else {
    echo "Lesson not found.";
    exit();
}

// Fetch the lesson content for this lesson
$contentSql = "SELECT * FROM lesson_Content WHERE lessonID = ?";
$contentStmt = $db_connection->prepare($contentSql);
$contentStmt->bind_param("i", $lessonID);
$contentStmt->execute();
$contentResult = $contentStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lesson['name']); ?> - Lesson Content</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .lesson-content {
            max-width: 900px;
            margin: 0 auto;
        }
        .lesson-content h2 {
            text-align: center;
        }
        .lesson-content img {
            width: 300px;
            height: auto;
            margin-bottom: 20px;
        }
        .lesson-content p {
            font-size: 1.2em;
            line-height: 1.5;
        }
        .content-item {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .lesson-content .back-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .lesson-content .back-button:hover {
            background-color: #0056b3;
        }
        .lesson-video, .lesson-pdf {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="lesson-content">
    <h2><?php echo htmlspecialchars($lesson['name']); ?></h2>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($lesson['date']); ?></p>
    <p><strong>Price:</strong> Rs. <?php echo htmlspecialchars($lesson['price']); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($lesson['description']); ?></p>

    <?php if ($contentResult->num_rows > 0): ?>
        <?php while ($content = $contentResult->fetch_assoc()): ?>
            <div class="content-item">
                <!-- Lesson Content: Image -->
                <?php if (!empty($content['contentPicture'])): ?>
                    <div class="lesson-image">
                        <img src="<?php echo htmlspecialchars($content['contentPicture']); ?>" alt="Content Image">
                    </div>
                <?php endif; ?>

                 \<!-- Lesson Content: PDF -->
                <?php if (!empty($content['pdfFile'])): ?>
                    <div class="lesson-pdf">
                        <p><strong>PDF File:</strong> <?php echo htmlspecialchars(basename($content['pdfFile'])); ?></p>
                    </div>
                <?php endif; ?>


                <!-- Lesson Content: Text Description -->
                <?php if (!empty($content['contentDescription'])): ?>
                    <div class="lesson-description">
                        <p><strong>Content Description:</strong> <?php echo nl2br(htmlspecialchars($content['contentDescription'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No content available for this lesson.</p>
    <?php endif; ?>

    <a href="teachlessons.php?teacherID=<?php echo urlencode($teacherID); ?>&name=<?php echo urlencode($fName . ' ' . $lName); ?>&subject=<?php echo urlencode($subject); ?>" class="back-button">Back to Lessons</a>
</div>
</body>
</html>
