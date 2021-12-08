<?php
include "../backend/functions.php";
session_start();
$errors = array();
$success = array();

check_session_variables();

if (!empty($_SESSION["success"])) {
    $success = $_SESSION["success"];
    unset($_SESSION["success"]);
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

// POST PUBLICATION
if (isset($_POST['post'])) {
    $id = $_SESSION['id'];
    $title = $_POST["post_title"];
    $content = $_POST["post_content"];

    if (empty($title)) {
        array_push($errors, "You have to specify a title");
    }

    if (empty($content)) {
        array_push($errors, "You have to write the content of the publication");
    }

    if (count($errors) == 0) {
        post($_SESSION["id"], $title, $content);
        array_push($success, "Publication posted");
        $_SESSION["success"] = $success;
        header("location: /home");
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="../sn/style.css?version=1">
</head>

<body>

    <div class="header">
        <h2>New Publication</h2>
    </div>

    <div class="content">
        <p><a class="btn" href="/home">Back</a></p>
        <?php require "../backend/popup.php" ?>
        <form method="post">
            <div class="input-group">
                <input type="text" name="post_title" placeholder="Title">
            </div>
            <div class="input-group">
                <textarea name="post_content" placeholder="Text"></textarea>
            </div>
            <div class="input-group">
                <button type="submit" class="btn" name="post">Post</button>
            </div>
        </form>
    </div>

</body>

</html>