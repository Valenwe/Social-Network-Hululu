<?php
include('../sn/backend/server.php');

if (!isset($_SESSION['username'])) {
    array_push($errors, "You must be logged in first");
    header('location: /login');
} 
?>
<!DOCTYPE html>
<html>

<head>
    <title>Account Settings</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../sn/style.css">
</head>

<body>
    <form method="post">
        <?php include('../sn/backend/popup.php'); ?>
        <p><a class="btn" href="/home">Back</a></p>
        <div class="input-group">
            <label>Add / Change First Name</label>
            <input type="text" name="set_firstname" , placeholder="<?php if (isset($_SESSION['firstname'])) echo $_SESSION['firstname'] ?>">
        </div>
        <div class="input-group">
            <label>Add / Change Last Name</label>
            <input type="text" name="set_lastname" , placeholder="<?php if (isset($_SESSION['lastname'])) echo $_SESSION['lastname'] ?>">
        </div>
        <div class="input-group">
            <button type="submit" class="btn" name="set_reset_name">Reset Firstname and Fullname</button>
        </div>
        <div class="input-group">
            <label>Change username</label>
            <input type="text" name="set_username" , placeholder="<?php echo $_SESSION['username'] ?>">
        </div>
        <div class="input-group">
            <label>Change email</label>
            <input type="email" name="set_email" , placeholder="<?php echo $_SESSION['email'] ?>">
        </div>
        <div class="input-group">
            <label>Old Password</label>
            <input type="password" name="set_old_password">
        </div>
        <div class="input-group">
            <label>New Password</label>
            <input type="password" name="set_new_password">
        </div>
        <div class="input-group">
            <button type="submit" class="btn" name="set_change">Save</button>
        </div>
    </form>
</body>

</html>