<?php
include('backend/server.php');
check_session_variables();

if (isset($_GET['showfollowing'])) {
    $_SESSION["showmode"] = "following";
    header('location: /friends.php');
}

if (isset($_GET['showfollower'])) {
    $_SESSION["showmode"] = "follower";
    header('location: /friends.php');
}

// pour le back button
if (isset($_SESSION["search"]))
    $last_search = "/search.php";
else
    $last_search = "/home.php";

$publications = get_most_recent_publication(10, false);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Account Page</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css?version=1">
</head>

<body>
    <div class="header">
        <h2>Account page</h2>
    </div>
    <div class="content">
        <p><a class="btn" href="<?php echo $last_search ?>">Back</a></p>
        <img class='avatar' src='<?php echo $_SESSION["avatar"]; ?>'>

        <?php require "backend/popup.php" ?>
        <p>Name: <?php if (isset($_SESSION['firstname']) && isset($_SESSION['lastname'])) echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></p>
        <p>Username: <strong><?php echo $_SESSION['username']; ?></strong></p>
        <a href="/me.php?showfollower=1">
            <p>Followers: <?php if (!empty($_SESSION['follower'])) echo count($_SESSION['follower']);
                            else echo 0; ?></p>
        </a>
        <a href='/me.php?showfollowing=1'>
            <p>Following: <?php if (!empty($_SESSION['following'])) echo count($_SESSION['following']);
                            else echo 0 ?></p>
        </a>
    </div>
    <div>
        <?php display_publications($publications, false) ?>
    </div>
    </div>

    <script src="backend/jquery.min.js"></script>
    <script src="backend/publications.js"></script>
</body>

</html>
