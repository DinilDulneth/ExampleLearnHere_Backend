<?php
    session_start();


require 'dbConfig.php';

// Initialize error message variable
$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

        // Prepare the SQL query
        $sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$password' ;";
        $result = $db_connection ->query($sql);


        if($result -> num_rows > 0){
            while($row = $result -> fetch_assoc()){
                $adminID = $row["adminID"];
                $email = $row["email"];

                //Storing data in the session
                $_SESSION['adminID'] = $adminID;
                $_SESSION['email'] = $email;
            }
        }


        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
                    
                    header("Location: adminDashboard.php");
            } else {
                $error = "Invalid email or password. Try again!";
            }
        } else {
            // Log database errors for debugging
            error_log("Database error: " . $db_connection->error);
            $error = "An internal error occurred. Please try again later.";
        }

        if (!empty($error)) {
            echo "<script>
                    alert('$error');
                    window.location.href='adminLogin.php';
                  </script>";
            exit;
        }


?>