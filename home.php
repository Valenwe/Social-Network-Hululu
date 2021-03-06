<?php
session_start();
include "backend/functions.php";

check_session_variables();

$errors = array();
$success = array();

if (!empty($_SESSION["success"])) {
    $success = $_SESSION["success"];
    unset($_SESSION["success"]);
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("location: /login.php");
}

if (isset($_GET['settings']))
    header("location: /settings.php");


if (isset($_GET['account']))
    header("location: /me.php");


if (isset($_GET['search'])) {
    if (!preg_match("/^[a-zA-Z0-9-_]*$/", $_POST['ind_search_content'])) {
        array_push($errors, "Incorrect characters");
    } else {
        $_SESSION['search'] = addslashes(strip_tags($_POST['ind_search_content']));
        header("location: /search.php");
    }
} else {
    // pour retirer la dernière recherche qui ne sert plus
    if (isset($_SESSION["search"]))
        unset($_SESSION["search"]);
}

if (isset($_GET['new_post']))
    header("location: /post.php");

if (isset($_GET['pm']))
    header("location: /pm.php");

$displayed_publications = get_most_recent_publication(5, true);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="style.css?version=1">
</head>

<body>

    <div class="header">
        <h2>Home Page</h2>
        <p> <a href="/home.php?new_post=1" style="color:purple;">New publication</a></p>
        <p> <a href="/home.php?pm=1" style="color:gray;">Private messages</a></p>
        <p> <a href="/home.php?account=1" style="color:green;">Account</a></p>
        <p> <a href="/home.php?settings='1'" style="color:blue;">Settings</a></p>
        <p> <a href="/home.php?logout='1'" style="color: red;">Logout</a> </p>

        <form method="post" action="/home.php?search=1">
            <div class="input-group">
                <input type="text" name="ind_search_content" placeholder="Search">
            </div>
            <div class="input-group">
                <button type="submit" class="btn" name="ind_search">Search</button>
            </div>
        </form>
    </div>
    <div class="content">
        <!-- notification message -->
        <?php require('backend/popup.php'); ?>

        <!-- logged in user information -->
        <?php if (isset($_SESSION['username'])) : ?>
            <p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
            <p> <?php if (isset($_SESSION['firstname']) && isset($_SESSION['lastname'])) echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></p>
        <?php endif ?>
    </div>

    <div class="content publications">
        <?php display_publications($displayed_publications, false) ?>
    </div>


    <script src="backend/jquery.min.js"></script>
    <script src="backend/publications.js"></script>
</body>

</html>
