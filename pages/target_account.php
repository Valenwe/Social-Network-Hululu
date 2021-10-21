<?php
include('../backend/server.php');

if (!isset($_SESSION['username'])) {
    array_push($errors, "You must be logged in first");
    $_SESSION["errors"] = $errors;
    header("location: /login");
}

if (isset($_SESSION['target_search'])) {
    $search = $_SESSION['target_search'];
    $query = "SELECT * FROM users WHERE username='$search'";
    $result = mysqli_query($db, $query);

    if ($result)
        $target =  mysqli_fetch_assoc($result);
}

if (isset($_GET["friend"])) {
    if (add_follow($db, $target)) {
        $target = refresh_user($db, $target);
        array_push($success, "Friend added");
    } else {
        array_push($errors, "Error trying to add friend");
    }
}

if (isset($_GET["unfriend"])) {
    if (remove_follow($db, $target)) {
        $target = refresh_user($db, $target);
        array_push($success, "Friend removed");
    } else {
        array_push($errors, "Error trying to remove friend");
    }
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
        <?php include "../backend/popup.php" ?>
        <p><a class="btn" href="/search">Back</a></p>
        <?php if (isset($target)) : ?>
            <?php if (!is_following($target["id"])) : ?>
                <p><a href=<?php echo $_SERVER["REQUEST_URI"] . "?friend=1" ?>>Add friend</a></p>
            <?php else : ?>
                <p><a href=<?php echo $_SERVER["REQUEST_URI"] . "?unfriend=1" ?>>Remove friend</a></p>
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