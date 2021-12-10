<?php include('backend/server.php')?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css?version=1">
</head>

<body>
    <div class="header">
        <h2>Login account</h2>
    </div>

    <form method="post">
        <?php require('backend/popup.php'); ?>
        <div class="input-group">
            <label>Username</label>
            <input type="text" name="log_username">
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="log_password">
        </div>
        <div class="input-group">
            <button type="submit" class="btn" name="log_user">Login</button>
        </div>
        <p>
            Not yet a member? <a href="/register.php">Sign up</a>
        </p>
    </form>
</body>

</html>
