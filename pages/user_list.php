<?php
include('../backend/server.php');

if (!isset($_SESSION['username'])) {
    array_push($errors, "You must be logged in first");
    $_SESSION["errors"] = $errors;
    header('location: /login');
}

foreach ($_GET as $key => $value) {
    if (strpos($value, "user_") !== false) {
        $_SESSION['target_search'] = str_replace_first("user_", "", $value);
        // si on clique sur notre compte
        if ($_SESSION["username"] == $_SESSION["target_search"]) {
            header("location: /me");
        } else {
            header("location: /user=" . $_SESSION['target_search']);
        }
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Search</title>
    <link rel="stylesheet" type="text/css" href="../sn/style.css">
</head>

<body>
    <div class="header">
        <h2><?php if ($_SESSION["showmode"] == "following")
                echo "Following users";
            elseif ($_SESSION["showmode"] == "follower")
                echo "Follower users"; ?></h2>
    </div>
    <div class="content">
        <p><a class="btn" href="/me">Back</a></p>
        <?php

        if (isset($_SESSION["showmode"]) && isset($_SESSION[$_SESSION["showmode"]])) {
            $user_list = array();
            foreach ($_SESSION[$_SESSION["showmode"]] as $user_id) {
                $query = "SELECT * FROM users WHERE id='$user_id'";
                $result = mysqli_query($db, $query);
                array_push($user_list, mysqli_fetch_assoc($result));
            }

            if (count($user_list) > 0) {
                $plural = "";
                if (count($user_list) > 1) $plural = "s";
                if ($_SESSION["showmode"] == "following")
                    echo "You are following " . count($user_list) . " user" . $plural . "<br><br>";
                elseif ($_SESSION["showmode"] == "follower")
                    echo "You are being followed by " . count($user_list) . " user" . $plural . "<br><br>";
                foreach ($user_list as $user_target) {
                    $display = "Username: " . $user_target["username"];
                    if (!empty($user_target["firstname"]) || !empty($user_target["lastname"])) {
                        $display .= " - Name: ";
                        if (!empty($user_target["firstname"]))
                            $display .= $user_target["firstname"] . " ";

                        if (!empty($user_target["lastname"]))
                            $display .= $user_target["lastname"];
                    }
                    $search_username = $user_target['username'];
                    echo "<p><a href='/search?target=user_$search_username'>" . $display . "</a></p><br>";
                }
            } else {
                if ($_SESSION["showmode"] == "following")
                    echo "You have no following users";
                elseif ($_SESSION["showmode"] == "follower")
                    echo "You have no users following you";
            }
        } else {
            if ($_SESSION["showmode"] == "following")
                echo "You have no following users";
            elseif ($_SESSION["showmode"] == "follower")
                echo "You have no users following you";
        }

        ?>
    </div>
</body>

</html>