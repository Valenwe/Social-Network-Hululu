<?php
include('backend/server.php');

check_session_variables();

if (isset($_SESSION['target_search'])) {
    $target_username = $_SESSION['target_search'];
} else {
    $target_username = str_replace("/user=", "", strtok($_SERVER["REQUEST_URI"], "?"));
}

$target = find("users", array("username" => $target_username), 1);

if (isset($_GET["friend"])) {
    if (add_follow($target)) {
        $target = find("users", array("username" => $target_username), 1);
        array_push($success, "Friend added");
    } else {
        array_push($errors, "Error trying to add friend");
    }
}

if (isset($_GET["unfriend"])) {
    if (remove_follow($target)) {
        $target = find("users", array("username" => $target_username), 1);
        array_push($success, "Friend removed");
    } else {
        array_push($errors, "Error trying to remove friend");
    }
}

if (isset($_GET["send_pm"])) {
    header("location: /pm.php");
}


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
        <?php include "backend/popup.php" ?>
        <p><a class="btn" href="/search.php">Back</a></p>
        <img class='avatar' src='<?php echo $target['avatar']; ?>'>
        <?php if (isset($target)) : ?>
            <?php if (!is_following($target["id"])) : ?>
                <p><a href=<?php echo strtok($_SERVER["REQUEST_URI"], "?") . "?friend=1" ?>>Add friend</a></p>
            <?php else : ?>
                <p><a href=<?php echo strtok($_SERVER["REQUEST_URI"], "?")  . "?unfriend=1" ?>>Remove friend</a></p>

                <?php if (is_follower($target["id"])) : ?>
                    <p><a href=<?php echo strtok($_SERVER["REQUEST_URI"], "?")  . "?send_pm=1" ?>>Send message</a></p>
                <?php endif ?>
            <?php endif ?>



            <p>Name: <?php if (!empty($target['firstname']) && !empty($target['lastname'])) echo $target['firstname'] . " " . $target['lastname']; ?></p>
            <p>Username: <?php echo $target['username']; ?></p>
            <p>Followers: <?php if (!empty($target['follower'])) echo count(explode(" ", $target['follower']));
                            else echo 0; ?></p>
            <p>Following: <?php if (!empty($target['following'])) echo count(explode(" ", $target['following']));
                            else echo 0; ?></p>
        <?php endif ?>

        <?php if (!isset($target)) : ?>
            <p>Error trying to display the informations</p>
        <?php endif ?>
    </div>
</body>

</html>
