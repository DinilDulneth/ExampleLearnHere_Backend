<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php
    // Collect GET data
    $lessonID = isset($_GET['lessonID']) ? $_GET['lessonID'] : '';
    $teacherID = isset($_GET['teacherID']) ? $_GET['teacherID'] : '';
    $studentID = isset($_GET['paramstd']) ? $_GET['paramstd'] : '';
    $lessonPrice = isset($_GET['price']) ? $_GET['price'] : '';
    $date = date('Y-m-d'); // Current date
    $time = date('H:i:s'); // Current time
?>
<form action="stdprocessPaymentSlip.php" method="POST" enctype="multipart/form-data">
    <br>Student ID:<input type="text" name="studentID" value="<?php echo $studentID; ?>">
    <br>Teacher ID:<input type="text" name="teacherID" value="<?php echo $teacherID; ?>">
    <br>Lesson ID :<input type="text" name="lessonID" value="<?php echo $lessonID; ?>">
    <br>Lesson price:<input type="text" name="lessonPrice" value="<?php echo $lessonPrice; ?>">
    <br><label for="paymentSlip">Upload Payment Slip:</label>
    <br><input type="file" name="paymentSlip" id="paymentSlip" required>
    <br><button type="submit">Submit Payment</button>
</form>

</body>
</html>