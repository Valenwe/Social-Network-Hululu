<?php 
include('../sn/backend/server.php') ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Register account</title>
    <link rel="stylesheet" type="text/css" href="../sn/style.css">
</head>

<body>
    <div class="header">
        <h2>Register</h2>
    </div>

    <form method="post">
        <?php include('../sn/backend/popup.php'); ?>
        <div class="input-group">
            <label>Username</label>
            <input type="text" name="reg_username">
        </div>
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="reg_email">
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="reg_password_1">
        </div>
        <div class="input-group">
            <label>Confirm password</label>
            <input type="password" name="reg_password_2">
        </div>
        <div class="input-group">
            <button type="submit" class="btn" name="reg_user" value="1">Register</button>
        </div>
        <p>
            Already a member? <a href="/login">Sign in</a>
        </p>
    </form>
</body>

</html>