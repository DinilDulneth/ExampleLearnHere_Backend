<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student login History</title>
</head>
<body>
    <?php
    require 'dbConfig.php';
        $sql = "SELECT * FROM teacherloginhistory";

        $result = $db_connection ->query($sql);

        //get student login history details
        if($result -> num_rows > 0){
            while($row = $result -> fetch_assoc()){
                echo $row["loginID"];
                echo " ";
                echo $row["teacherID"];
                echo " ";
                echo $row["loginDate"];
                echo " ";
                echo $row["time"];
                echo " ";
                echo $row["deviceFingerprint"];
                echo " ";
                echo $row["deviceInfo"];
                echo " ";
                echo "<br>";
            }
        }
    ?>
</body>
</html>