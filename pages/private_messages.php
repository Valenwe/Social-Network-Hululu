<?php
include('../backend/server.php');

check_session_variables();

$new_pm_target = -1;
if (isset($_SESSION['target_search'])) {
    $query = "SELECT id FROM users WHERE username='" . $_SESSION["target_search"] . "'";
    $new_pm_target = mysqli_fetch_assoc(mysqli_query($db, $query))["id"];
    unset($_SESSION["target_search"]);
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Private Messages</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../sn/style.css">
</head>

<body>
    <div class="header">
        <h2>Private Messages</h2>
    </div>
    <div class="content">
        <p><a class="btn" href="/home">Back</a></p>
        <?php echo display_pms($_SESSION["id"], $new_pm_target) ?>
    </div>
    <script src="../sn/backend/jquery.min.js"></script>
    <script src="../sn/backend/private_messages.js"></script>
</body>

</html>