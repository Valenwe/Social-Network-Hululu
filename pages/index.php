<?php
session_start();
$errors = array();
$_SESSION["success"] = array();

if (!isset($_SESSION['username'])) {
    array_push($errors, "You must be logged in first");
    header('location: /login');
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['id']);
    header("location: /login");
}

if (isset($_GET['settings'])) {
    header("location: /settings");
}

if (isset($_GET['account'])) {
    header("location: /me");
}

if (isset($_GET['search'])) {
    $_SESSION['search'] = $_POST['ind_search_content'];
    header("location: /search");
}

// POST
if (isset($_POST['ind_post'])) {
    $id = $_SESSION['id'];
    $title = $_POST["ind_post_title"];
    $content = $_POST["ind_post_content"];
    array_push($_SESSION['success'], "Publication posted");
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="../sn/style.css">
</head>

<body>

    <div class="header">
        <h2>Home Page</h2>
        <p> <a href="/home?account=1" style="color:green;">Account</a></p>
        <p> <a href="/home?settings='1'" style="color:blue;">Settings</a></p>
        <p> <a href="/home?logout='1'" style="color: red;">Logout</a> </p>

        <form method="post" action="/home?search=1">
            <div class="input-group">
                <input type="text" name="ind_search_content" placeholder="Search">
            </div>
            <div class="input-group">
                <button type="submit" class="btn" name="ind_search">Search</button>
            </div>
        </form>

        <form method="post">
            <div class="input-group">
                <input type="text" name="ind_post_title" placeholder="Title">
            </div>
            <div class="input-group">
                <input type="text" name="ind_post_content" placeholder="Text">
            </div>
            <div class="input-group">
                <button type="submit" class="btn" name="ind_post">Post</button>
            </div>
        </form>
    </div>
    <div class="content">
        <!-- notification message -->
        <?php include('../backend/popup.php'); ?>

        <!-- logged in user information -->
        <?php if (isset($_SESSION['username'])) : ?>
            <p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
            <p> <?php if (isset($_SESSION['firstname']) && isset($_SESSION['lastname'])) echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></p>
        <?php endif ?>
    </div>

</body>

</html>