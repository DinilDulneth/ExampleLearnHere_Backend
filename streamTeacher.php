

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Teachers</title>
    <style>
           body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .teacher-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .teacher-box {
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .teacher-box img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .teacher-box h3 {
            margin: 0 0 10px;
        }
        .teacher-box p {
            margin: 5px 0;
        }
        .teacher-box a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .teacher-box a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Our Teachers</h1>

    <?php
    // Include database connection
    require 'dbConfig.php';

    // Fetch all teachers
    $sql = "SELECT * 
            FROM teacher 
            WHERE subject IN ('Biology', 'Chemistry', 'Physics', 'Combined Mathematics', 'Agricultural Science', 'ICT');
            ";
    $result = $db_connection->query($sql);
    ?>
    <div class="teacher-container"><h2>Science Stream</h2><br>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>

                <a href="teachlessons.php?teacherID=<?php echo urlencode($row['teacherID']); ?>&name=<?php echo urlencode($row['fName'] . ' ' . $row['lName']); ?>&subject=<?php echo urlencode($row['subject']); ?>">
                <div class="teacher-box">
                    <img src="<?php echo htmlspecialchars($row['profilePicture']); ?>" alt="Profile Picture">
                    <h3><?php echo htmlspecialchars($row['fName'] . ' ' . $row['lName']); ?></h3>
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
                    <p><strong>School:</strong> <?php echo htmlspecialchars($row['school']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                    View Lessons</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No teachers available.</p>
        <?php endif; ?>
    </div>

    <?php
    // Include database connection
    require 'dbConfig.php';

    // Fetch all teachers
    $sql = "SELECT * 
            FROM teacher 
            WHERE subject IN ('Accounting', 'Business Studies', 'Economics', 'ICT', 'Business Statistics');
            ";
    $result = $db_connection->query($sql);
    ?>
    <div class="teacher-container"><h2>Commerce Stream</h2><br>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <a href="teachlessons.php?teacherID=<?php echo urlencode($row['teacherID']); ?>&name=<?php echo urlencode($row['fName'] . ' ' . $row['lName']); ?>&subject=<?php echo urlencode($row['subject']); ?>">
                <div class="teacher-box">
                <img src="<?php echo htmlspecialchars($row['profilePicture']); ?>" alt="Profile Picture">
                    <h3><?php echo htmlspecialchars($row['fName'] . ' ' . $row['lName']); ?></h3>
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
                    <p><strong>School:</strong> <?php echo htmlspecialchars($row['school']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                    View Lessons</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No teachers available.</p>
        <?php endif; ?>
    </div>


    <?php
    // Include database connection
    require 'dbConfig.php';

    // Fetch all teachers
    $sql = "SELECT * 
            FROM teacher 
            WHERE subject IN (
                'Sinhala Language and Literature', 
                'Tamil Language and Literature', 
                'English Literature', 
                'History', 
                'Geography', 
                'Political Science', 
                'Logic and Scientific Method', 
                'Buddhist Civilization', 
                'Hindu Civilization', 
                'Christian Civilization', 
                'Islam Civilization', 
                'Fine Arts', 
                'Media Studies', 
                'Communication and Media Studies', 
                'Sociology', 
                'Psychology', 
                'Economics', 
                'ICT'
            );
            ";
    $result = $db_connection->query($sql);
    ?>
    <div class="teacher-container"><h2>Art Stream</h2><br>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <a href="teachlessons.php?teacherID=<?php echo urlencode($row['teacherID']); ?>&name=<?php echo urlencode($row['fName'] . ' ' . $row['lName']); ?>&subject=<?php echo urlencode($row['subject']); ?>">
                <div class="teacher-box">
                <img src="<?php echo htmlspecialchars($row['profilePicture']); ?>" alt="Profile Picture">
                    <h3><?php echo htmlspecialchars($row['fName'] . ' ' . $row['lName']); ?></h3>
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
                    <p><strong>School:</strong> <?php echo htmlspecialchars($row['school']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                    View Lessons</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No teachers available.</p>
        <?php endif; ?>
    </div>


    <?php
    // Include database connection
    require 'dbConfig.php';

    // Fetch all teachers
    $sql = "SELECT * 
            FROM teacher 
            WHERE subject IN ('Science for Technology', 'Engineering Technology', 'Bio-Systems Technology', 'ICT');
            ";
    $result = $db_connection->query($sql);
    ?>
    <div class="teacher-container"><h2>Technology Stream</h2><br>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <a href="teachlessons.php?teacherID=<?php echo urlencode($row['teacherID']); ?>&name=<?php echo urlencode($row['fName'] . ' ' . $row['lName']); ?>&subject=<?php echo urlencode($row['subject']); ?>">
                <div class="teacher-box">
                <img src="<?php echo htmlspecialchars($row['profilePicture']); ?>" alt="Profile Picture">
                    <h3><?php echo htmlspecialchars($row['fName'] . ' ' . $row['lName']); ?></h3>
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
                    <p><strong>School:</strong> <?php echo htmlspecialchars($row['school']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                    View Lessons</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No teachers available.</p>
        <?php endif; ?>
    </div>



</body>
</html>
