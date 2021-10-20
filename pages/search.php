<?php
include('../sn/backend/server.php');

if (!isset($_SESSION['username'])) {
    array_push($errors, "You must be logged in first");
    header('location: /login');
}

foreach ($_GET as $key => $value)
{
    if (strpos($value, "user_") !== false) {
        $_SESSION['target_search'] = str_replace_first("user_", "", $value);
        header("location: /user=" . $_SESSION['target_search']);
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
        <h2>Search result</h2>
    </div>
    <div class="content">
        <p><a class="btn" href="/home">Back</a></p>
        <?php

        if (isset($_SESSION['search'])) {
            $target = $_SESSION['search'];
            $query = "SELECT * FROM users WHERE username LIKE '%$target%'";
            $result = mysqli_query($db, $query);

            if (mysqli_num_rows($result) > 0) {
                echo mysqli_num_rows($result) . " results found" . "<br><br>";
                while ($row = mysqli_fetch_assoc($result)) {
                    $display = "Username: " . $row["username"];
                    if (!empty($row["firstname"]) || !empty($row["lastname"])) {
                        $display .= " - Name: ";
                        if (!empty($row["firstname"]))
                            $display .= $row["firstname"] . " ";

                        if (!empty($row["lastname"]))
                            $display .= $row["lastname"];
                    }
                    $search_username = $row['username'];
                    echo "<p><a href='/search?target=user_$search_username'>" . $display . "</a></p><br>";
                }
            } else {
                echo "No results";
            }
        } else {
            echo "No results";
        }

        ?>
    </div>
</body>

</html>