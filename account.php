<?php include('server.php') ?>
<!DOCTYPE html>
<html>

<head>
    <title>Account Page</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="header">
        <h2>Account page</h2>
    </div>
    <div class="content">
        <button class="btn" onclick="history.back()">Back</button>
        <p>Name: <?php if (isset($_SESSION['firstname']) && isset($_SESSION['lastname'])) echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></p>
        <p>Username: <strong><?php echo $_SESSION['username']; ?></strong></p>
        <p>Followers: <?php if (isset($_SESSION['followers'])) echo count(json_decode($_SESSION['followers'], true));
                        else echo 0; ?></p>
        <p>Following: <?php if (isset($_SESSION['following'])) echo count(json_decode($_SESSION['following'], true));
                        else echo 0; ?></p>
    </div>
</body>

</html>