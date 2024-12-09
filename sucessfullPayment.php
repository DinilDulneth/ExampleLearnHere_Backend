<?php
// Start session
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .success-message {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .success-message h1 {
            color: #28a745;
        }
        .success-message p {
            font-size: 1.2em;
        }
        .success-message a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .success-message a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="success-message">
        <h1>Payment Successful!</h1>
        <p>Thank you for your payment. Your transaction has been processed successfully.</p>
        <p>You can now access your purchased lessons.</p>
        <a href="stdDashboard.php">Go to Dashboard</a>
    </div>
</body>
</html>
