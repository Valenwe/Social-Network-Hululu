<?php
include('../backend/server.php');

if (!isset($_SESSION['username'])) {
    array_push($errors, "You must be logged in first");
    header('location: /login');
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Account Page</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../sn/style.css">
</head>

<body>
    <div class="header">
        <h2>Account page</h2>
    </div>
    <div class="content">
        <p><a class="btn" href="/home">Back</a></p>
        <p>Name: <?php if (isset($_SESSION['firstname']) && isset($_SESSION['lastname'])) echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></p>
        <p>Username: <strong><?php echo $_SESSION['username']; ?></strong></p>
        <p>Followers: <?php if (!empty($_SESSION['followers'])) echo count(json_decode($_SESSION['followers'], true));
                        else echo 0; ?></p>
        <p>Following: <?php if (!empty($_SESSION['following'])) echo count(json_decode($_SESSION['following'], true));
                        else echo 0; ?></p>
    </div>
</body>

</html>