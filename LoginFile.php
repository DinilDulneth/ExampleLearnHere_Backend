<?php

require 'dbConfig.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = $_POST['lemail'];
    $psw = $_POST['lpassword'];
    $deviceFingerprint = $_POST['deviceFingerprintlog'];

    function loginStudent($db_connection,$email,$psw,$deviceFingerprint ){

        $sql = "SELECT * 
            FROM student 
            WHERE email = '$email' 
                AND password = '$psw' 
                AND (
                        device1 = '$deviceFingerprint'
                     OR device2 = '$deviceFingerprint'
                     OR device3 = '$deviceFingerprint')";

        $result = $db_connection ->query($sql);

        if($result->num_rows == 1){
            $row = $result->fetch_assoc();

            $sql2 = "SELECT * 
            FROM student 
            WHERE email = '$email' 
                AND password = '$psw' 
                AND (
                        device1 = '$deviceFingerprint'
                     OR device2 = '$deviceFingerprint'
                     OR device3 = '$deviceFingerprint')";
            $result2 = $db_connection ->query($sql2);    

            //recode the student login 
            //get studentID details
            if($result2 -> num_rows > 0){
                $row = $result2 -> fetch_assoc();
                    $studentID =  $row["studentID"];
                    $sFname = $row["fName"];
                    $sTeacherID = $row["teacherID"];
                    $sProfilePic = $row["profilePicture"];

                    session_start();

                    $_SESSION['studentID'] = $studentID;
                    $_SESSION['fName'] = $sFname;
                    $_SESSION['sTeacherID'] = $sTeacherID;
                    $_SESSION['sprofilePicture'] = $sProfilePic;

            }
            echo "<script>
                    alert('Welcome to the LearnHere E-Lerning platform!');
                    window.location.href = 'stdDashboard.php';
                  </script>";

            $loginDate = date("Y-m-d"); 
            $time = date("H:i:s"); 
            $deviceInfo = $_SERVER['HTTP_USER_AGENT'];

            $sql_1 = "INSERT INTO studentloginhistory (studentID, loginDate, time, deviceFingerprint, deviceInfo) 
                VALUES (?, ?, ?, ?, ?);";
        
            $stmt = $db_connection->prepare($sql_1);
            $stmt->bind_param("sssss", $studentID, $loginDate, $time, $deviceFingerprint, $deviceInfo);

            if ($stmt->execute()) {
                echo "<script>console.log('insert date')<script/>";
            } else {
                echo "Error: " . $stmt->error;
            }

            
        }else {
            echo "<script>alert('Error during login. Please try again.');
            window.location.href = 'LoginRegisterStd.php';
            </script>";
        }
    }

            $sql_2 = "SELECT * FROM student where email = '$email' ";
            $result_2 = $db_connection->query($sql_2);

        if($result_2 -> num_rows > 0){
            while($row = $result_2 -> fetch_assoc()){
                $fp1 = $row["device1"];
                $fp2 = $row["device2"];
                $fp3 = $row["device3"];
                $permission = $row['permission'];

            if($permission == 1){

                if($fp1 == $deviceFingerprint || $fp2 == $deviceFingerprint || $fp3 == $deviceFingerprint){
                    loginStudent($db_connection,$email,$psw,$deviceFingerprint );
                }
                else{
                    if($fp1 == NULL){
                        $sql_update = "UPDATE student SET device1 = ? WHERE email = ?";
                        $stmt = $db_connection->prepare($sql_update);
                        $stmt->bind_param("ss", $deviceFingerprint, $email);
                        $stmt->execute();
                        //echo "Device registered as device1.";
                        loginStudent($db_connection,$email,$psw,$deviceFingerprint );
                    }
                    else if($fp2 == NULL){
                        $sql_update = "UPDATE student SET device2 = ? WHERE email = ?";
                        $stmt = $db_connection->prepare($sql_update);
                        $stmt->bind_param("ss", $deviceFingerprint, $email);
                        $stmt->execute();
                        //echo "Device registered as device2.";
                        loginStudent($db_connection,$email,$psw,$deviceFingerprint );
                    }
                    else if($fp3 == NULL){
                        $sql_update = "UPDATE student SET device3 = ? WHERE email = ?";
                        $stmt = $db_connection->prepare($sql_update);
                        $stmt->bind_param("ss", $deviceFingerprint, $email);
                        $stmt->execute();
                        //echo "Device registered as device3.";
                        loginStudent($db_connection,$email,$psw,$deviceFingerprint );
                    }
                    else{
                        echo "<script>alert('Device limit reached. Contact Your Teacher.');
                        window.location.href = 'LoginRegisterStd.php';
                        </script>";
                    }
                }
            }else{
                echo "<script>alert('Your account is not approved yet. Please contact your teacher.');
                window.location.href = 'LoginRegisterStd.php';
                </script>";
            }        
            }
        }else{
                echo "<script>alert('Email not found. Please check your email and try again.');
                window.location.href = 'LoginRegisterStd.php';
                </script>";
            }
}
?>