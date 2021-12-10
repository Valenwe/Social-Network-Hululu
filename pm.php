<?php
include('backend/server.php');

check_session_variables();

$new_pm_target = -1;
if (isset($_SESSION['target_search'])) {
    $new_pm_target = find("users", array("username" => $_SESSION["target_search"]), 1, array("id"))["id"];
    unset($_SESSION["target_search"]);
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Private Messages</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css?version=1">
</head>

<body>
    <div class="header">
        <h2>Private Messages</h2>
    </div>
    <div class="content">
        <p><a class="btn" href="/home.php">Back</a></p>
        <?php echo display_pms($_SESSION["id"], $new_pm_target) ?>
    </div>
    <script src="backend/jquery.min.js"></script>
    <script src="backend/private_messages.js"></script>
</body>

</html>