<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <h1>Admin Login</h1>
    <?php if (!empty($error)) { ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php } ?>
    <form method="POST" action="adminLoginConfig.php">
        <label for="email">email:</label><br>
        <input type="text" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
