<?php
include('../sn/backend/server.php');

if (!isset($_SESSION['username'])) {
    array_push($errors, "You must be logged in first");
    header("location: /login");
}

if (isset($_SESSION['target_search'])) {
    $search = $_SESSION['target_search'];
    $query = "SELECT * FROM users WHERE username='$search'";
    $result = mysqli_query($db, $query);

    if ($result)
        $target =  mysqli_fetch_assoc($result);
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
        <p><a class="btn" onclick="history.back()">Back</a></p>
        <?php if (isset($target)) : ?>
            <p>Name: <?php if (!empty($target['firstname']) && !empty($target['lastname'])) echo $target['firstname'] . " " . $target['lastname']; ?></p>
            <p>Username: <strong><?php echo $target['username']; ?></strong></p>
            <p>Followers: <?php if (!empty($target['followers'])) echo count(json_decode($target['followers'], true));
                            else echo 0; ?></p>
            <p>Following: <?php if (!empty($target['following'])) echo count(json_decode($target['following'], true));
                            else echo 0; ?></p>
        <?php endif ?>

        <?php if (!isset($target)) : ?>
            <p>Error trying to display the informations</p>
        <?php endif ?>
    </div>
</body>

</html>