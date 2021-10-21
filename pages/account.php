<?php
include('../backend/server.php');

if (!isset($_SESSION['username'])) {
    array_push($errors, "You must be logged in first");
    $_SESSION["errors"] = $errors;
    header('location: /login');
}

if (isset($_GET['showfollowing'])) {
    $_SESSION["showmode"] = "following";
    header('location: /friends');
}

if (isset($_GET['showfollower'])) {
    $_SESSION["showmode"] = "follower";
    header('location: /friends');
}

if (isset($_GET["delete"])) {
    $post_id = $_GET["delete"];
    $query = "DELETE FROM publications WHERE post_id='$post_id'";
    $result = mysqli_query($db, $query);
    if ($result) {
        array_push($success, "Publications deleted successfully");
    } else {
        array_push($errors, "Error trying to remove the publications");
    }
}

// pour le back button
if (isset($_SESSION["search"]))
    $last_search = "/search";
else
    $last_search = "/home";

$publications = get_publication($db, $_SESSION["id"]);

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
        <p><a class="btn" href="<?php echo $last_search ?>">Back</a></p>
        <?php require "../backend/popup.php" ?>
        <p>Name: <?php if (isset($_SESSION['firstname']) && isset($_SESSION['lastname'])) echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></p>
        <p>Username: <strong><?php echo $_SESSION['username']; ?></strong></p>
        <a href="/me?showfollower=1">
            <p>Followers: <?php if (!empty($_SESSION['follower'])) echo count($_SESSION['follower']);
                            else echo 0; ?></p>
        </a>
        <a href='/me?showfollowing=1'>
            <p>Following: <?php if (!empty($_SESSION['following'])) echo count($_SESSION['following']);
                            else echo 0 ?></p>
        </a>
    </div>
    <?php display_publications($publications) ?>
    </div>
</body>

</html>